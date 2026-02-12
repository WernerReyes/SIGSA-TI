<?php
namespace App\Services;

use Carbon\Carbon;

class BusinessHoursService
{

    private readonly HolidayService $holidayService;    

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }
    
    public function addBusinessMinutes(Carbon $start, int $minutes): Carbon
    {
        $current = $start->copy();

        while ($minutes > 0) {

            // Obtener horario del día actual
            $schedule = $this->getScheduleForDay($current);

            // Si no hay horario (domingo)
            if (!$schedule) {
                $current->addDay()->setTime(8, 30);
                continue;
            }

                
            // TODO: Add Holiday check
            if ($this->holidayService->isHoliday($current)) {
                $current->addDay()->setTime(8, 30);
                continue;
            }
                

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




    public  function businessMinutesBetween(Carbon $start, Carbon $end): int
    {
        $minutes = 0;
        $current = $start->copy();

        while ($current->lt($end)) {

            $nextMinute = $current->copy()->addMinute();

            if ($this->isBusinessTime($current)) {
                $minutes++;
            }

            $current = $nextMinute;
        }

        return $minutes;
    }



    private  function isBusinessTime(Carbon $date): bool
    {
        $schedule = $this->getScheduleForDay($date);

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


    


    private  function getScheduleForDay(Carbon $date): ?array
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
}
