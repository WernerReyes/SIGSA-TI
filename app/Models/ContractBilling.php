<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractBilling extends Model
{
    protected $table = 'contract_billing';

    protected $fillable = [
        'contract_id',
        'amount',
        'currency',
        'frequency',
        'billing_cycle_days',
        'auto_renew',
        'next_billing_date',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
