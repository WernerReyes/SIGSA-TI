<?php

namespace App\Models;

use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketRequestType;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use Illuminate\Database\Eloquent\Model;


class Ticket extends Model
{
    //
    protected $table = 'tickets_sistema';

    protected $primaryKey = 'id_tickets';

    protected $fillable = [
        'type',
        'title',
        'description',
        'opened_at',
        'closed_at',
        'status',
        'priority',
        'request_type',
        'requester_id',
        'technician_id',
        'updated_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'status' => TicketStatus::class,
        'priority' => TicketPriority::class,
        'type' => TicketType::class,
        'request_type' => TicketRequestType::class,
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function scopeOrderedByPriority($query)
    {
        return $query->orderByRaw("
        CASE priority 
            WHEN '" . TicketPriority::URGENT->value . "' THEN 0 
            WHEN '" . TicketPriority::HIGH->value . "' THEN 1 
            WHEN '" . TicketPriority::MEDIUM->value . "' THEN 2 
            WHEN '" . TicketPriority::LOW->value . "' THEN 3 
            ELSE 4 
        END
    ");
    }


}

