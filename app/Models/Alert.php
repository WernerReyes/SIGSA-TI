<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $table = 'alerts';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'type',
        'message',
        'status',
        'last_notified_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_notified_at' => 'datetime',
    ];
}
