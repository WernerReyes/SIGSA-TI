<?php

namespace App\Models;

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
        'return_date',
        'comment',
    ];


    protected $casts = [
        'assigned_at' => 'date',
        'return_date' => 'date',
    ];
    

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function deliveryRecord()
    {
        return $this->hasOne(DeliveryRecord::class, 'assignment_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
