<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brands';

    protected $fillable = [
        'name',
    ];

    public function models()
    {
        return $this->hasMany(AssetModel::class, 'brand_id');
    }

    public function types()
    {
        return $this->belongsToMany(AssetType::class, 'asset_type_brand', 'brand_id', 'asset_type_id')->withTimestamps();
    }
}
