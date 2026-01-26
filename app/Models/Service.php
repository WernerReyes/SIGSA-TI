<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    protected $table = 'services';

    public $timestamps = true;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'url',
        'username',
        'password',
        'description',
        'is_active',
    ];
}
