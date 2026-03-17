<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentEmailLog extends Model
{
    protected $table = 'assignment_email_logs';

    protected $fillable = [
        'assignment_id',
        'delivery_record_id',
        'sent_by',
        'document_type',
        'recipient_email',
        'subject',
        'message',
        'document_path',
        'extra_image_names',
        'sent_at',
    ];

    protected $casts = [
        'extra_image_names' => 'array',
        'sent_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(AssetAssignment::class, 'assignment_id');
    }

    public function deliveryRecord()
    {
        return $this->belongsTo(DeliveryRecord::class, 'delivery_record_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by', 'staff_id');
    }
}
