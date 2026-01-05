<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiration' => 'date',
    ];



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

    public function histories()
    {
        return $this->hasMany(AssetHistory::class, 'asset_id')->orderBy('performed_at', 'desc');
    }

}
