<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'id',
        'type',
        'notifiable_id',
        'notifiable_type',
        'data',
        'read_at',
    ];

    public function contract()
    {
        return $this->hasOne(
            Contract::class,
            'id',
            'entity_id'
        );
    }



}