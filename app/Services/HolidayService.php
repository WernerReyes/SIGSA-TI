<?php

namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class HolidayService
{
    public function getHolidays(): array
    {
        $year = now()->year;
        $cacheKey = "holidays:{$year}";

        return Cache::remember(
            $cacheKey,
            $this->secondsUntilEndOfYear(),
            function () use ($year) {
                $response = file_get_contents($this->getURL($year));

                if ($response === false) {
                    throw new \Exception('Error al obtener los días festivos');
                }

                return json_decode($response, true);
            }
        );
    }

    private function getURL(int $year): string
    {
        return "https://date.nager.at/api/v3/PublicHolidays/{$year}/PE";
    }

    private function secondsUntilEndOfYear(): int
    {
        return now()->diffInSeconds(
            now()->copy()->endOfYear()
        );
    }

    public function isHoliday(Carbon $date): bool
    {
        return collect($this->getHolidays())
            ->contains(fn ($holiday) => $holiday['date'] === $date->toDateString());
    }
}


