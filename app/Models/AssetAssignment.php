<?php

namespace App\Models;

use App\Enums\DeliveryRecord\DeliveryRecordType;
use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    //

    protected $table = 'assets_assignments';

    public $timestamps = false;

    protected $fillable = [
        'asset_id',
        'assigned_to_id',
        'assigned_at',
        'returned_at',
        'comment',
        'return_comment',
        'responsible_id',
    ];


    protected $casts = [
        'assigned_at' => 'date',
        'returned_at' => 'date',
    ];


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
        return $this->hasOne(DeliveryRecord::class, 'assignment_id')->where('type', DeliveryRecordType::ASSIGNMENT->value)->latestOfMany();
    }

    public function returnDocument()
    {
        return $this->hasOne(DeliveryRecord::class, 'assignment_id')->where('type', DeliveryRecordType::DEVOLUTION->value)->latestOfMany();
    }

    

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
