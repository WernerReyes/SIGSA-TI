<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    //
    protected $table = 'assets';

    protected $fillable = [
        'name',
        'type',
        'status',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'warranty_expiration',
        'assigned_to',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiration' => 'date',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'staff_id');
    }

}
