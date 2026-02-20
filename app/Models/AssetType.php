<?php

namespace App\Models;

use App\Enums\Asset\AssetTypeCategory;
use App\Enums\Asset\AssetTypeEnum;
use App\Enums\Department\Allowed;
use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    //
    protected $table = 'assets_type';

    protected $fillable = [
        'name',
        // 'is_accessory',
        'doc_category',
        'created_at',
        'updated_at',
        'is_deletable'
    ];

    protected $appends = ['is_accessory'];

    public function scopeIsFromRRHH($query)
    {
        $isFromRRHH = auth()->user()->dept_id == Allowed::RRHH->value;
        return $query->when($isFromRRHH, function ($q) {
            $q->whereIn('id', AssetTypeEnum::RRHHTypes());
        });
    }

    public function getIsAccessoryAttribute()
    {
        return $this->doc_category === AssetTypeCategory::ACCESSORY->value;
    }
}
