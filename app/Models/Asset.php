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
        'serial_number',
        'purchase_date',
        'warranty_expiration',
        'assigned_to_id',
        'type_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiration' => 'date',
    ];



    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id', 'staff_id');
    }

}
