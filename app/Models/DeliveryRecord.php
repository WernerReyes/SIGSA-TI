<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Storage;

class DeliveryRecord extends Model
{
    //
    protected $table = 'delivery_records';

    protected $fillable = [
        'file_path',
        'assignment_id',
    ];

    public function getFileUrlAttribute()
    {
        return Storage::disk('public')->url($this->file_path);
    }

    protected $appends = [
        'file_url',
    ];

}
