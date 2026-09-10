<?php

namespace App\Services;

use Carbon\Carbon;

class RenditionDeadlineService
{
    /** The policy agreed for this module: Monday through Friday. */
    public function deadlineFor(Carbon|string $routeEndDate): Carbon
    {
        $date = Carbon::parse($routeEndDate)->startOfDay();
        $daysAdded = 0;

        while ($daysAdded < 5) {
            $date->addDay();

            if ($date->isWeekday()) {
                $daysAdded++;
            }
        }

        return $date;
    }

    public function isExpired(Carbon|string $routeEndDate, ?Carbon $today = null): bool
    {
        return ($today ?? now())->startOfDay()->greaterThan($this->deadlineFor($routeEndDate));
    }
}
