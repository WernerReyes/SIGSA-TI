<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractRenewal extends Model
{

protected $table = 'contract_renewals';

    protected $fillable = [
        'old_end_date',
        'new_end_date',
        'notes',
        'contract_id',
        'renewed_by_id',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function renewedBy()
    {
        return $this->belongsTo(User::class, 'renewed_by_id', 'staff_id');
    }
}
