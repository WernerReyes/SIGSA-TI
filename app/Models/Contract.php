<?php

namespace App\Models;

use App\Enums\notification\NotificationEntity;
use DB;
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


    protected static function booted()
    {
        static::deleting(function ($contract) {

            DB::transaction(function () use ($contract) {
                $contract->billing()->delete();
                $contract->expiration()->delete();
                $contract->renewals()->delete();
                $contract->notifications()->delete();
            });
        });
    }


    public function billing()
    {
        return $this->hasOne(ContractBilling::class);
    }

    public function expiration()
    {
        return $this->hasOne(ContractExpiration::class);
    }

    public function renewals()
    {
        return $this->hasMany(ContractRenewal::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'entity_id')->where('type', NotificationEntity::CONTRACT->value);
    }

}
