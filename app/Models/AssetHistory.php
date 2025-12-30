<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetHistory extends Model
{
    //
    protected $table = 'assets_histories';

    public $timestamps = false;

    protected $fillable = [
        'action',
        'asset_id',
        'performed_by',
        'performed_at',
    ];


    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by', 'staff_id');
    }
}
