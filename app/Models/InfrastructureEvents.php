<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfrastructureEvents extends Model
{
    protected $table = 'infrastructure_events';

    protected $fillable = [
        'title',
        'description',
        'type',
        'date',
        'responsible_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function responsible()
    {
        return $this->belongsTo( User::class, 'responsible_id', 'staff_id');
    }
}
