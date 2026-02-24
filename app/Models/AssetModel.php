<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetModel extends Model
{
    protected $table = 'models';

    protected $fillable = [
        'name',
    ];
}
