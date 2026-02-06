<?php

namespace App\Console\Commands;

use App\Enums\Contract\BillingFrequency;
use App\Models\ContractBilling;
use App\Notifications\ContractRenewalNotification;
use App\Services\UserService;
use Auth;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckContractBilling extends Command
{
    protected $signature = 'app:check-contract-billing';

    public function handle()
    {
        $today = Carbon::today();

        ContractBilling::query()
            ->where('auto_renew', true)
            ->whereDate('next_billing_date', '<=', $today)
            ->get()
            ->each(function (ContractBilling $billing) {

                // 🔔 alertar renovación
                $this->notify($billing);


                // 🔄 calcular siguiente fecha
                $billing->update([
                    'next_billing_date' =>
                        Carbon::parse($billing->next_billing_date)
                            ->addMonths(BillingFrequency::getMonth($billing->frequency)),
                ]);
            });
    }

    protected function notify(ContractBilling $billing)
    {
        // 👉 EJEMPLOS DE NOTIFICACIÓN



        try {
            // 1️⃣ Log (mínimo viable)
            app(UserService::class)->getTIDepartmentUsers()
                ->each(function ($user) use ($billing) {
                    // ds('Notificar por contrato', $billing->contract_id, 'al usuario', $user);
                    //  $user->notify(new ContractRenewalNotification($billing));
                    ds(
                        $user->getKey(),       // debe mostrar staff_id
                        $user->getKeyName(),   // "staff_id"
                        $user->routeNotificationFor('broadcast'),
                    );
                    $user->notify(new ContractRenewalNotification($billing));
                });
        } catch (\Exception $e) {
            logger()->error('Error al enviar notificación de renovación', [
                'contract_id' => $billing->contract_id,
                'error' => $e->getMessage(),
            ]);
        }



    }
}
