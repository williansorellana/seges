<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RenditionDeadlineService
{
    public function deadlineFor(Carbon|string $routeEndDate): Carbon
    {
        return $this->addBusinessDays($routeEndDate, 5);
    }

    public function addBusinessDays(Carbon|string $from, int $days): Carbon
    {
        $date = Carbon::parse($from, 'America/Santiago')->setTimezone('America/Santiago')->startOfDay();
        $daysAdded = 0;
        $calendars = [];
        while ($daysAdded < $days) {
            $date->addDay();
            if (! isset($calendars[$date->year])) {
                $calendars[$date->year] = $this->holidaysForYear($date->year);
            }
            if ($date->isWeekday() && ! in_array($date->toDateString(), $calendars[$date->year], true)) {
                $daysAdded++;
            }
        }

        return $date;
    }

    private function holidaysForYear(int $year): array
    {
        $defaults = config("business_calendar.$year");
        // A reviewed year uses the editable calendar; defaults support migration
        // backfills before the seeded calendar has been written.
        if (DB::table('business_calendar_years')->where('year', $year)->exists()) {
            return DB::table('business_holidays')->whereYear('date', $year)->pluck('date')->all();
        }
        if (is_array($defaults)) {
            return array_keys($defaults);
        }
        throw ValidationException::withMessages(['calendar' => "Debe completarse el calendario de feriados de $year antes de calcular este plazo."]);
    }

    public function isExpired(Carbon|string $routeEndDate, ?Carbon $today = null): bool
    {
        return ($today ?? now())->startOfDay()->greaterThan($this->deadlineFor($routeEndDate));
    }

    public function remainingBusinessDays(Carbon $deadline): int
    {
        $date = now('America/Santiago')->startOfDay();
        $remaining = 0;
        $calendars = [];
        while ($date->lt($deadline)) {
            $date->addDay();
            $calendars[$date->year] ??= $this->holidaysForYear($date->year);
            if ($date->isWeekday() && ! in_array($date->toDateString(), $calendars[$date->year], true)) {
                $remaining++;
            }
        }

        return $remaining;
    }
}
