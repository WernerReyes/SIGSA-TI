<?php

namespace App\Console\Commands;

use App\DTOs\AdminControl\RenewContractDto;
use App\Enums\Contract\BillingFrequency;
use App\Enums\Contract\ContractType;
use App\Models\ContractBilling;
use App\Notifications\ContractRenewalNotification;
use App\Services\AdminControlService;
use App\Services\UserService;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class CheckContractBilling extends Command
{
    protected $signature = 'app:check-contract-billing';


    public function handle()
    {
        $today = Carbon::today();

        ContractBilling::query()
            ->where('auto_renew', true)
            ->whereDate('next_billing_date', $today)
            ->with('contract')
            ->get()
            ->each(function (ContractBilling $billing) {
                try {
          
    
                    DB::transaction(function () use ($billing) {

                        app(AdminControlService::class)->autoRenew($billing);

                        // 🔔 Notify
                        $this->notify($billing);
                    });

                } catch (\Exception $e) {
                    ds('Error al renovar contrato automáticamente', [
                        'contract_id' => $billing->contract_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            });


    }


    protected function notify(ContractBilling $billing)
    {

        try {
            app(UserService::class)->getTIDepartmentUsers()
                ->each(function ($user) use ($billing) {
                    ds("Enviando notificación de renovación de contrato a {$user->full_name} ({$user->email}) para el contrato {$billing->contract->name}");
                    $user->notify(new ContractRenewalNotification($billing));
                });
        } catch (\Exception $e) {
            ds('Error al enviar notificación de renovación de contrato', [
                'contract_id' => $billing->contract_id,
                'error' => $e->getMessage(),
            ]);
            logger()->error('Error al enviar notificación de renovación de contrato', [
                'contract_id' => $billing->contract_id,
                'error' => $e->getMessage(),
            ]);
        }

    }

}
