<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractExpiration extends Model
{
    protected $table = 'contract_expirations';

    protected $fillable = [
        'contract_id',
        'expiration_date',
        'alert_days_before',
        'notified',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
