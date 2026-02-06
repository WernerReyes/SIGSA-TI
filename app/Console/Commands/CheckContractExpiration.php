<?php

namespace App\Console\Commands;

use App\Models\ContractExpiration;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckContractExpiration extends Command
{
    protected $signature = 'app:check-contract-expiration';

    protected $description = 'Check and notify about contract expirations';

    public function handle()
    {
        $today = Carbon::today();

        ContractExpiration::query()
            ->where('notified', false)
            ->whereDate('expiration_date', '>=', $today) // aún no vencido
            ->get()
            ->each(function (ContractExpiration $expiration) use ($today) {

                $alertDate = Carbon::parse($expiration->expiration_date)
                    ->subDays($expiration->alert_days_before);

                if ($today->greaterThanOrEqualTo($alertDate)) {
                    $this->notify($expiration);

                    $expiration->update([
                        'notified' => true,
                    ]);
                }
            });

        return Command::SUCCESS;
    }

    protected function notify(ContractExpiration $expiration)
    {
        // 👉 EJEMPLOS DE NOTIFICACIÓN

        // 1️⃣ Log (mínimo viable)
        logger()->info('Contrato por vencer', [
            'contract_id' => $expiration->contract_id,
            'expiration_date' => $expiration->expiration_date,
        ]);

        ds('Notificar por contrato', $expiration->contract_id);

        // 2️⃣ Notificación interna (ejemplo)
        /*
        foreach ($expiration->contract->responsibles as $user) {
            $user->notify(new ContractExpirationNotification($expiration));
        }
        */

        // 3️⃣ Mail / Slack / etc (más adelante)
    }
}
