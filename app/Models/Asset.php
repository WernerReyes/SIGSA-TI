<?php

namespace App\Models;

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
        'assigned_to_id',
        'type_id',
        'is_new',
        'invoice_path'
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

            });

            // 🔴 FUERA de la transacción
            foreach ($filesToDelete as $file) {
                Storage::disk('public')->delete($file);
            }
        });
    }




    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id', 'staff_id');
    }

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
        return $this->hasMany(AssetAssignment::class, 'asset_id')->orderBy('assigned_at', 'desc');
    }

    public function histories()
    {
        return $this->hasMany(AssetHistory::class, 'asset_id')->orderBy('performed_at', 'desc');
    }



}
