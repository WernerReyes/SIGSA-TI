<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetReparation extends Model
{
    protected $table = 'asset_reparations';

    protected $fillable = [
        'asset_id',
        'description',
        'date',
        'image_paths',
    ];

    protected $casts = [
        'date' => 'date',
        'image_paths' => 'array',
    ];


}
