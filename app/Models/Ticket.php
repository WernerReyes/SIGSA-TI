<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

enum TicketStatus: string
{
    case OPEN = 'OPEN';
    case IN_PROGRESS = 'IN_PROGRESS';
    case RESOLVED = 'RESOLVED';
    case CLOSED = 'CLOSED';
}


enum TicketPriority: string
{
    case LOW = 'LOW';
    case MEDIUM = 'MEDIUM';
    case HIGH = 'HIGH';
    case URGENT = 'URGENT';
}

enum TicketType: string
{
    case INDIDENT = 'INCIDENT';
    case SERVICE_REQUEST = 'SERVICE_REQUEST';
}

enum TicketRequestType: string
{
    case HARDWARE = 'HARDWARE';
    case SOFTWARE = 'SOFTWARE';
    case NETWORK = 'NETWORK';
    case OTHER = 'OTHER';
}

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


}

