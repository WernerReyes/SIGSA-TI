<?php

namespace App\Models;

use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketRequestType;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use DB;
use Illuminate\Database\Eloquent\Model;


class Ticket extends Model
{
    //
    protected $table = 'tickets_sistema';


    public $timestamps = true;

    protected $fillable = [
        'type',
        'title',
        'description',
        // 'opened_at',
        'closed_at',
        'status',
        'priority',
        'request_type',
        'requester_id',
        'responsible_id',
        // 'updated_at',
    ];

    protected $casts = [
        // 'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        // 'status' => TicketStatus::class,
        // 'priority' => TicketPriority::class,
        // 'type' => TicketType::class,
        // 'request_type' => TicketRequestType::class,
    ];

    protected static function booted()
    {
        static::deleting(function ($ticket) {

            DB::transaction(function () use ($ticket, &$filesToDelete) {
                $ticket->histories()->delete();
            });
        });
    }


    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function histories()
    {
        return $this->hasMany(TicketHistory::class, 'ticket_id', 'id');
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

