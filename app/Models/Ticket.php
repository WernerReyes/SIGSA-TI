<?php

namespace App\Models;

use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketRequestType;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Ticket extends Model
{
    //
    protected $table = 'system_tickets';


    public $timestamps = true;

    protected $fillable = [
        'type',
        'title',
        'description',
        // 'opened_at',
        // 'closed_at',
        'status',
        'category',
        'impact',
        'urgency',
        'priority',
        'category',
        'requester_id',
        'responsible_id',
        'images',


        'sla_response_due_at',
        'sla_resolution_due_at',

        'first_response_at',
        'resolved_at',

        'sla_paused_at',
        'sla_paused_duration',

        'sla_breached',

        // 'created_at',



        // 'updated_at',
    ];

    //   $table->timestamp('sla_response_due_at')->nullable();
    //         $table->timestamp('sla_resolution_due_at')->nullable();

    //         $table->timestamp('first_response_at')->nullable();
    //         $table->timestamp('resolved_at')->nullable();

    protected $casts = [
            'images' => 'array',
        // 'opened_at' => 'datetime',
        // 'closed_at' => 'datetime',
        // 'status' => TicketStatus::class,
        // 'priority' => TicketPriority::class,
        // 'type' => TicketType::class,
        // 'request_type' => TicketRequestType::class,
    ];


    protected $appends = ['images_urls'];

    public function getImagesUrlsAttribute()
    {
        if (!$this->images || !is_array($this->images)) {
            return null;
        }

        return array_map(function ($path) {
            return Storage::disk('public')->url($path);
        }, $this->images);
    }


    

    protected static function booted()
    {
        static::deleting(function ($ticket) {

            DB::transaction(function () use ($ticket, &$filesToDelete) {
                $ticket->histories()->delete();
            });
        });
    }

    
    public function assetEvents()
    {
        return $this->hasMany(TicketAsset::class, 'ticket_id', 'id');
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

