<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevelopmentProgress extends Model
{
    protected $table = 'development_progress';

    protected $fillable = [
        'notes',
        'percentage',
        'development_request_id',
        'performed_by',
    ];

    public function developmentRequest()
    {
        return $this->belongsTo(DevelopmentRequest::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by', 'staff_id');
    }
}
