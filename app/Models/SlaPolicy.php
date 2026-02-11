<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaPolicy extends Model
{
  
    protected $table = 'slas_policies';
protected $fillable = [
        'priority',
        'response_time_minutes',
        'resolution_time_minutes',
        // 'is_active',
    ];

    public $timestamps = false;

    
}
