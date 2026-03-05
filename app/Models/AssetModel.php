<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetModel extends Model
{
    protected $table = 'models';

    protected $fillable = [
        'name',
        'brand_id',
        'asset_type_id',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function type()
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }
}
