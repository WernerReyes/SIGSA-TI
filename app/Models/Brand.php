<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brands';

    protected $fillable = [
        'name',
        'type_id',
    ];

    public function type()
    {
        return $this->belongsTo(AssetType::class, 'type_id');
    }

    public function models()
    {
        return $this->hasMany(AssetModel::class, 'brand_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'brand_id');
    }
}
