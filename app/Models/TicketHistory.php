<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    //
    protected $table = 'ticket_histories';

    public $timestamps = false;

    protected $fillable = [
        'action',
        'description',
        'ticket_id',
        'performed_by',
        'performed_at',
    ];

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by', 'staff_id');
    }
}
