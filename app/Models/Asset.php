<?php

namespace App\Models;


use App\DTOs\Asset\AssetFiltersDto;
use App\Enums\Asset\AssetTypeEnum;
use App\Enums\Department\Allowed;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Asset extends Model
{
    //
    protected $table = 'assets';

    protected $fillable = [
        'name',
        'status',
        'brand',
        'model',
        'color',
        'serial_number',
        'processor',
        'ram',
        'storage',
        'purchase_date',
        'warranty_expiration',
        'type_id',
        'is_new',
        'notes',
        'invoice_path',
        'phone',
        'imei',
    ];

    public function getInvoiceUrlAttribute()
    {
        if (!$this->invoice_path) {
            return null;
        }
        return Storage::disk('public')->url($this->invoice_path);
    }


    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiration' => 'date',

    ];

    protected $appends = [
        'invoice_url',
        'full_name',
    ];


    protected static function booted()
    {
        static::deleting(function ($asset) {

            $filesToDelete = [];

            DB::transaction(function () use ($asset, &$filesToDelete) {

                // Guardar factura
                if ($asset->invoice_path && Storage::disk('public')->exists($asset->invoice_path)) {
                    $filesToDelete[] = $asset->invoice_path;
                }

                $asset->histories()->delete();

                foreach ($asset->assignments as $assignment) {

                    foreach ($assignment->deliveryRecords as $record) {
                        if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                            $filesToDelete[] = $record->file_path;
                        }
                    }


                    $assignment->deliveryRecords()->delete();
                }

                $asset->assignments()->delete();
                $asset->reparations()->delete();

            });

            // 🔴 FUERA de la transacción
            foreach ($filesToDelete as $file) {
                Storage::disk('public')->delete($file);
            }
        });
    }


    public function getFullNameAttribute()
    {
        $parts = [];

        if ($this->brand) {
            $parts[] = $this->brand;
        }

        if ($this->model) {
            $parts[] = $this->model;
        }

        if ($this->serial_number) {
            $parts[] = "S/N: {$this->serial_number}";
        }

        // Si hay partes adicionales, las concatenamos con espacios y guiones
        if (!empty($parts)) {
            return "{$this->name} (" . implode(' - ', $parts) . ")";
        }

        // Si no hay nada extra, solo devolvemos el nombre
        return $this->name;
    }



    public function scopeIsFromRRHH($query)
    {
        $isFromRRHH = auth()->user()->dept_id == Allowed::RRHH->value;
        return $query->when($isFromRRHH, function ($q) {
            $q->whereIn('type_id', AssetTypeEnum::RRHHTypes())

            ;
        });
    }


    public function scopeFilters($query, AssetFiltersDto $filtersDto, $stat = false)
    {
        return $query

        ->when(!$stat, function ($q) {
            $q->where(function ($q2) {
                $q2->whereDoesntHave('currentAssignment')
                    ->orWhereHas(
                        'currentAssignment',
                        fn($q3) =>
                        $q3->whereNull('parent_assignment_id')
                    );
            });
        })
        
        // ->where(function ($q) {
        //     $q->whereDoesntHave('currentAssignment')
        //         ->orWhereHas(
        //             'currentAssignment',
        //             fn($q2) =>
        //             $q2->whereNull('parent_assignment_id')
        //         );
        // })

            /*
            |--------------------------------------------------------------------------
            | BÚSQUEDA GENERAL
            |--------------------------------------------------------------------------
            */
            ->when($filtersDto->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhereHas(
                            'currentAssignment.childrenAssignments.asset',
                            fn($q2) =>
                            $q2->where('name', 'like', "%{$search}%")
                                ->orWhere('brand', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%")
                                ->orWhere('serial_number', 'like', "%{$search}%")
                        );
                });
            })

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */
            ->when(!empty($filtersDto->status), function ($query) use ($filtersDto) {
                $query->where(function ($q) use ($filtersDto) {
                    $q->whereIn('status', $filtersDto->status)
                        ->orWhereHas(
                            'currentAssignment.childrenAssignments.asset',
                            fn($q2) =>
                            $q2->whereIn('status', $filtersDto->status)
                        );
                });
            })

            /*
            |--------------------------------------------------------------------------
            | TIPO DE ACTIVO
            |--------------------------------------------------------------------------
            */
            ->when($filtersDto->types, function ($query, $typeId) use ($stat) {
                $query->where(function ($q) use ($typeId, $stat) {
                     $q->whereIn('type_id', $typeId)->when(!$stat, function ($q2) use ($typeId) {
                        $q2->orWhereHas(
                            'currentAssignment.childrenAssignments.asset',
                            fn($q3) =>
                            $q3->whereIn('type_id', $typeId)
                        );
                     });

                });
            })

            /*
            |--------------------------------------------------------------------------
            | ASIGNADO A USUARIO
            |--------------------------------------------------------------------------
            */
            ->when(!empty($filtersDto->assigned_to), function ($query) use ($filtersDto) {
                $ids = array_filter($filtersDto->assigned_to, fn($v) => !is_null($v));

                $query->where(function ($q) use ($ids, $filtersDto) {

                    if (!empty($ids)) {
                        $q->whereHas(
                            'currentAssignment',
                            fn($q2) =>
                            $q2->whereIn('assigned_to_id', $ids)
                        );
                    }

                    if (in_array(null, $filtersDto->assigned_to, true)) {
                        $q->orWhereDoesntHave('currentAssignment');
                    }
                });
            })

            /*
            |--------------------------------------------------------------------------
            | DEPARTAMENTO
            |--------------------------------------------------------------------------
            */
            ->when(!empty($filtersDto->department_id), function ($query) use ($filtersDto) {
                $query->whereHas(
                    'currentAssignment.assignedTo',
                    fn($q2) =>
                    $q2->whereIn('dept_id', $filtersDto->department_id)
                );
            })


            /* -------------------------------------------------------------------------
            | RANGO DE FECHAS DE CREACIÓN
            ------------------------------------------------------------------------- 
            */
            ->when($filtersDto->startDate, function ($query) use ($filtersDto) {
                $query->whereDate('created_at', '>=', $filtersDto->startDate);
            })->when($filtersDto->endDate, function ($query) use ($filtersDto) {
                $query->whereDate('created_at', '<=', $filtersDto->endDate);
            });
    }


    // public function assignedTo()
    // {
    //     return $this->belongsTo(User::class, 'assigned_to_id', 'staff_id');
    // }

    public function type()
    {
        return $this->belongsTo(AssetType::class, 'type_id');
    }

    public function currentAssignment()
    {
        return $this->hasOne(AssetAssignment::class, 'asset_id')->whereNull('returned_at');
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class, 'asset_id')->orderBy('assigned_at', 'desc')->orderBy('id', 'desc');
    }

    public function reparations()
    {
        return $this->hasMany(AssetReparation::class, 'asset_id')->orderBy('date', 'desc')->orderBy('id', 'desc');
    }

    public function histories()
    {
        return $this->hasMany(AssetHistory::class, 'asset_id')->orderBy('performed_at', 'desc')->orderBy('id', 'desc');
    }





}
