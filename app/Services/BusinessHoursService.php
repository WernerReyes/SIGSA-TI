<?php
namespace App\Services;

use Carbon\Carbon;

class BusinessHoursService
{

    public static function addBusinessMinutes2(Carbon $start, int $minutes): Carbon
    {
        $current = $start->copy();

        while ($minutes > 0) {

            // Obtener horario del día actual
            $schedule = self::getScheduleForDay2($current);

            // Si no hay horario (domingo)
            if (!$schedule) {
                $current->addDay()->setTime(8, 30);
                continue;
            }


            // TODO: Add Holiday check
//             if (Holiday::whereDate('date', $current->toDateString())->exists()) {
//     $current->addDay()->setTime(8, 30);
//     continue;

            // }

            foreach ($schedule as [$startHour, $startMinute, $endHour, $endMinute]) {

                $workStart = $current->copy()->setTime($startHour, $startMinute);
                $workEnd = $current->copy()->setTime($endHour, $endMinute);

                if ($current->lt($workStart)) {
                    $current = $workStart;
                }

                if ($current->gte($workStart) && $current->lt($workEnd)) {

                    $availableMinutes = $current->diffInMinutes($workEnd);

                    if ($minutes <= $availableMinutes) {
                        return $current->addMinutes($minutes);
                    }

                    $minutes -= $availableMinutes;
                    $current = $workEnd;
                }
            }
            // Si está después del horario → ir al siguiente día
            if ($current->gte($workEnd)) {
                $current->addDay()->setTime(8, 30);
                continue;
            }
        }

        return $current;
    }

    public static function addBusinessMinutes(Carbon $start, int $minutes): Carbon
    {
        $current = $start->copy();

        while ($minutes > 0) {

            // Obtener horario del día actual
            $schedule = self::getScheduleForDay($current);

            // Si no hay horario (domingo)
            if (!$schedule) {
                $current->addDay()->setTime(8, 30);
                continue;
            }


            // TODO: Add Holiday check
//             if (Holiday::whereDate('date', $current->toDateString())->exists()) {
//     $current->addDay()->setTime(8, 30);
//     continue;

            // }

            [$startHour, $startMinute, $endHour, $endMinute] = $schedule;

            $workStart = $current->copy()->setTime($startHour, $startMinute);
            $workEnd = $current->copy()->setTime($endHour, $endMinute);

            // Si está antes del horario → mover al inicio
            if ($current->lt($workStart)) {
                $current = $workStart;
            }

            // Si está después del horario → ir al siguiente día
            if ($current->gte($workEnd)) {
                $current->addDay()->setTime(8, 30);
                continue;
            }

            $availableMinutes = $current->diffInMinutes($workEnd);

            if ($minutes <= $availableMinutes) {
                return $current->addMinutes($minutes);
            }

            $minutes -= $availableMinutes;
            $current->addDay()->setTime(8, 30);
        }

        return $current;
    }



    public static function businessMinutesBetween(Carbon $start, Carbon $end): int
    {
        $minutes = 0;
        $current = $start->copy();

        while ($current->lt($end)) {

            $nextMinute = $current->copy()->addMinute();

            if (self::isBusinessTime($current)) {
                $minutes++;
            }

            $current = $nextMinute;
        }

        return $minutes;
    }



    private static function isBusinessTime2(Carbon $date): bool
    {
        $schedule = self::getScheduleForDay2($date);

        if (!$schedule) {
            return false;
        }

        foreach ($schedule as [$startHour, $startMinute, $endHour, $endMinute]) {

            $workStart = $date->copy()->setTime($startHour, $startMinute);
            $workEnd = $date->copy()->setTime($endHour, $endMinute);

            if ($date->gte($workStart) && $date->lt($workEnd)) {
                return true;
            }
        }

        return false;
    }


    private static function isBusinessTime(Carbon $date): bool
    {
        $schedule = self::getScheduleForDay($date);
        if (!$schedule) { //* Sunday
            return false;
        }

        // // Domingo
        // if ($date->dayOfWeekIso == 7) {
        //     return false;
        // }

        // Feriado
        // if (Holiday::whereDate('date', $date->toDateString())->exists()) {
        //     return false;
        // }

        [$startHour, $startMinute, $endHour, $endMinute] = $schedule;

        $workStart = $date->copy()->setTime($startHour, $startMinute);
        $workEnd = $date->copy()->setTime($endHour, $endMinute);

        return $date->between($workStart, $workEnd);

        // $hour = $date->hour;
        // $minute = $date->minute;

        // // Lunes a viernes
        // if ($date->dayOfWeekIso >= 1 && $date->dayOfWeekIso <= 5) {
        //     return ($hour > 8 || ($hour == 8 && $minute >= 30))
        //         && ($hour < 18);
        // }

        // // Sábado
        // if ($date->dayOfWeekIso == 6) {
        //     return ($hour > 8 || ($hour == 8 && $minute >= 30))
        //         && ($hour < 12);
        // }

        // return false;
    }


    private static function getScheduleForDay2(Carbon $date): ?array
    {
        switch ($date->dayOfWeekIso) {
            case 1: // Lunes a Viernes
            case 2:
            case 3:
            case 4:
            case 5:
                return [
                    [8, 30, 13, 0],  // Mañana
                    [14, 0, 18, 0],  // Tarde
                ];

            case 6: // Sábado
                return [
                    [8, 30, 12, 0],
                ];

            default:
                return null;
        }
    }


    private static function getScheduleForDay(Carbon $date): ?array
    {
        switch ($date->dayOfWeekIso) {
            case 1: // Lunes
            case 2:
            case 3:
            case 4:
            case 5:
                return [8, 30, 18, 0]; // 8:30 - 18:00

            case 6: // Sábado
                return [8, 30, 12, 0]; // 8:30 - 12:00

            default: // Domingo
                return null;
        }
    }
}
