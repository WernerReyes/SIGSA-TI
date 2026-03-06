<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetModel extends Model
{
    protected $table = 'models';

    protected $fillable = [
        'name',
        'brand_id',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'model_id');
    }
}
