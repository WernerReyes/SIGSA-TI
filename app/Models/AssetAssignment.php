<?php

namespace App\Models;

use App\Enums\Asset\AssetStatus;
use App\Enums\DeliveryRecord\DeliveryRecordType;
use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    //

    protected $table = 'assets_assignments';

    // public $timestamps = false;

    protected $fillable = [
        'asset_id',
        'assigned_to_id',
        'assigned_at',
        'returned_at',
        'comment',
        'return_comment',
        'responsible_id',
        'return_reason',
        'parent_assignment_id',
    ];

    protected $casts = [
        'assigned_at' => 'date',
        'returned_at' => 'datetime',
    ];

    public function canBeEdited(): bool
    {
        return $this->returned_at === null
            && $this->asset->status === AssetStatus::ASSIGNED->value
            && $this->created_at->diffInMinutes(now()) <= 15;
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function deliveryRecords()
    {
        return $this->hasMany(DeliveryRecord::class, 'assignment_id');
    }

    public function deliveryDocument()
    {
        return $this->hasOne(DeliveryRecord::class, 'assignment_id')
            ->ofMany(
                ['created_at' => 'max'], // columna para elegir el último
                function ($query) {
                    $query->where('type', DeliveryRecordType::ASSIGNMENT->value);
                }
            );
    }

    public function returnDocument()
    {
        return $this->hasOne(DeliveryRecord::class, 'assignment_id')
            ->ofMany(
                ['created_at' => 'max'],
                function ($query) {
                    $query->where('type', DeliveryRecordType::DEVOLUTION->value);
                }
            );
    }
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function childrenAssignments()
    {
        return $this->hasMany(AssetAssignment::class, 'parent_assignment_id');
    }


    public function parentAssignment()
    {
        return $this->belongsTo(AssetAssignment::class, foreignKey: 'parent_assignment_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
