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
                        ds('Verificando vencimiento de contrato', [
                            'contract_id' => $expiration->contract_id,
                            'expiration_date' => Carbon::parse($expiration->expiration_date)->endOfDay()->toDateString(),
                            'alert_date' => $alertDate->toDateString(),
                            'today' => $today->toDateString(),
                        ]);
                        if ($today->equalTo(Carbon::parse($expiration->expiration_date)->endOfDay())) {
                        ds('El contrato venció hoy', [
                            'contract_id' => $expiration->contract_id,
                            'expiration_date' => Carbon::parse($expiration->expiration_date)->toDateString(),
                            'today' => $today->toDateString(),
                        ]);
                                
                        $expiration->contract->update([
                                'status' => ContractStatus::EXPIRED->value,
                            ]);
                        }

                        if ($today->greaterThanOrEqualTo($alertDate)) {
                            ds('Enviando notificación de vencimiento de contrato', [
                                'contract_id' => $expiration->contract_id,
                                'expiration_date' => Carbon::parse($expiration->expiration_date)->toDateString(),
                                'alert_date' => $alertDate->toDateString(),
                                'today' => $today->toDateString(),
                            ]);
                            $this->notify($expiration);
                        }
                    });
                } catch (\Exception $e) {
                    ds('Error al enviar notificación de vencimiento de contrato', [
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
            ds('Error al enviar notificación de renovación de contrato', [
                'contract_id' => $expiration->contract_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
