<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'invoice_path',
    ];

    protected $appends = [
        'invoice_url',
    ];

    public function getInvoiceUrlAttribute()
    {
        if (!$this->invoice_path) {
            return null;
        }
        return Storage::disk('public')->url($this->invoice_path);
    }




    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by', 'staff_id');
    }

    public function deliveryRecord()
    {
        return $this->belongsTo(DeliveryRecord::class, 'related_delivery_record_id');
    }
}
