<?php

namespace App\Console\Commands;

use App\Enums\Contract\ContractStatus;
use App\Models\ContractExpiration;
use App\Notifications\ContractExpirationNotification;
use App\Services\UserService;
use Carbon\Carbon;
use DB;
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

                try {
                    DB::transaction(function () use ($expiration, $today, $alertDate) {


                        if ($today->isSameDay(Carbon::parse($expiration->expiration_date))) {
                            $expiration->contract->update([
                                'status' => ContractStatus::EXPIRED->value,
                            ]);
                        }

                        if ($today->greaterThanOrEqualTo($alertDate)) {
                            $this->notify($expiration);
                        }
                    });
                } catch (\Exception $e) {
                    logger()->error('Error al procesar la expiración del contrato', [
                        'contract_id' => $expiration->contract_id,
                        'error' => $e->getMessage(),
                    ]);
                }


            });

        return Command::SUCCESS;
    }

    protected function notify(ContractExpiration $expiration)
    {
        try {
            app(UserService::class)->getTIDepartmentUsers()
                ->each(function ($user) use ($expiration) {

                    $user->notify(new ContractExpirationNotification($expiration));
                });

            $expiration->update([
                'notified' => true,
            ]);
        } catch (\Exception $e) {
            logger()->error('Error al enviar notificación de expiración de contrato', [
                'contract_id' => $expiration->contract_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
