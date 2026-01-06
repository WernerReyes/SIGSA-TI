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
        'description',
        'performed_by',
        'performed_at',
        'related_delivery_record_id',
    ];


    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by', 'staff_id');
    }

    public function deliveryRecord()
    {
        return $this->belongsTo(DeliveryRecord::class, 'related_delivery_record_id');
    }
}
