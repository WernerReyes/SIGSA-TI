<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevelopmentRequestDeveloper extends Model
{
    // TODO: Change the name of the model to development_request_developers
    protected $table = 'development_request_developers';

    protected $fillable = [
        'development_request_id',
        'developer_id',
    ];

    // public $timestamps = false;

    public function developmentRequest()
    {
        return $this->belongsTo(DevelopmentRequest::class, 'development_request_id');
    }
}


// $table->unsignedBigInteger('development_request_id');
//             $table->unsignedInteger('developer_id');

//             $table->foreign('development_request_id')
//                 ->references('id')
//                 ->on('development_requests');

//             $table->foreign('developer_id')
//                 ->references('staff_id')
//                 ->on('ost_staff');