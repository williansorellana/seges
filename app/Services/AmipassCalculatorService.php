<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AmipassCalculatorService
{
    private array $holidays = [
        '2026-04-03',
        '2026-05-01',
        '2026-05-21',
        '2026-06-20',
        '2026-06-29',
        '2026-07-16',
        '2026-08-15',
        '2026-09-18',
        '2026-09-19',
        '2026-10-12',
        '2026-10-31',
        '2026-11-01',
        '2026-12-08',
        '2026-12-25',
    ];

    public function calculate(
        string $startDate,
        string $endDate,
        ?string $startTime,
        ?string $endTime,
        string $rateType = 'otros',
        bool $includesBreakfast = false,
        int $excludedLunches = 0,
        int $excludedDinners = 0,
        int $recipients = 1,
    ): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $businessDays = $this->businessDays($start, $end);

        $rates = $rateType === 'venta'
            ? ['breakfast' => 2000, 'lunch' => 6000, 'dinner' => 7000]
            : ['breakfast' => 2000, 'lunch' => 5000, 'dinner' => 5000];

        // La recarga corresponde a cada día hábil de la ruta. Las exclusiones se
        // limitan a las comidas realmente disponibles para no producir montos negativos.
        $breakfastCount = $includesBreakfast ? 0 : $businessDays;
        $lunchCount = max(0, $businessDays - min($businessDays, $excludedLunches));
        $dinnerCount = max(0, $businessDays - min($businessDays, $excludedDinners));
        $perPersonAmount = ($breakfastCount * $rates['breakfast'])
            + ($lunchCount * $rates['lunch'])
            + ($dinnerCount * $rates['dinner']);

        $amount = $perPersonAmount * max(1, $recipients);

        return [
            'business_days' => $businessDays,
            'amount' => max(0, $amount),
            'per_person_amount' => max(0, $perPersonAmount),
        ];
    }

    private function businessDays(Carbon $start, Carbon $end): int
    {
        if ($end->lt($start)) {
            return 0;
        }

        $count = 0;

        foreach (CarbonPeriod::create($start, $end) as $date) {
            if ($date->isWeekday() && !$this->isHoliday($date)) {
                $count++;
            }
        }

        return $count;
    }

    private function firstDayAmount(Carbon $start, ?string $startTime, ?string $endTime): int
    {
        if (!$startTime) {
            return 0;
        }

        if ($this->isSunday($start) || $this->isHoliday($start)) {
            return 0;
        }

        $startHour = (int) Carbon::parse($startTime)->format('H');
        $endHour = $endTime ? (int) Carbon::parse($endTime)->format('H') : null;

        if ($startHour <= 9) {
            return 12000;
        }

        if ($startHour > 9 && $endHour !== null && $endHour <= 18) {
            return 10000;
        }

        return 0;
    }

    private function middleDaysAmount(Carbon $start, Carbon $end, int $businessDays): int
    {
        $startSpecial = $this->isSunday($start) || $this->isHoliday($start);
        $endSpecial = $this->isSunday($end) || $this->isHoliday($end);

        if ($startSpecial && $endSpecial) {
            return $businessDays * 12000;
        }

        if ($startSpecial || $endSpecial) {
            return ($businessDays - 1) * 12000;
        }

        return ($businessDays - 2) * 12000;
    }

    private function lastDayAmount(Carbon $end, ?string $endTime): int
    {
        if (!$endTime) {
            return 0;
        }

        if ($this->isSunday($end) || $this->isHoliday($end)) {
            return 0;
        }

        $endHour = (int) Carbon::parse($endTime)->format('H');

        if ($endHour < 9) {
            return 2000;
        }

        if ($endHour > 12 && $endHour <= 18) {
            return 7000;
        }

        return 12000;
    }

    private function isSunday(Carbon $date): bool
    {
        return $date->dayOfWeekIso === 7;
    }

    private function isHoliday(Carbon $date): bool
    {
        return in_array($date->format('Y-m-d'), $this->holidays, true);
    }
}
