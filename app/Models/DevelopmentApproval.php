<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevelopmentApproval extends Model
{
    protected $table = 'development_approvals';

    protected $fillable = [
        'development_request_id',
        'approved_by_id',
        'status',
        'level',
        'comments',
    ];

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id', 'staff_id');
    }
}
