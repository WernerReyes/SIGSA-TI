<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAsset extends Model
{
    protected $table = 'ticket_assets';

    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'asset_id',
        'asset_assignment_id',
        'action',
        'performed_by',
        // 'notes',
    ];


    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
