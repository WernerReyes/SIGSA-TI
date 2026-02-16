<?php

namespace App\Models;

use App\Enums\Department\Allowed;
use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    //
    protected $table = 'assets_type';

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
    ];

    public function scopeIsFromRRHH($query)
    {
        $isFromRRHH = auth()->user()->dept_id == Allowed::RRHH->value;
        return $query->when($isFromRRHH, function ($q) {
            $q->whereIn('name', ['Celular', 'Accesorio']);
        });
    }
}
