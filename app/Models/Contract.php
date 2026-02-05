<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{

    protected $table = 'contracts';

    protected $fillable = [
        'name',
        'provider',
        'type',
        'period',
        'total_amount',
        'status',
        'start_date',
        'end_date',
        'notes',
    ];

    public function billing()
    {
        return $this->hasOne(ContractBilling::class);
    }

    public function expiration()
    {
        return $this->hasOne(ContractExpiration::class);
    }

}
