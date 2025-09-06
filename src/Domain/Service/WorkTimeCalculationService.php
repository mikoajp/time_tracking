<?php

namespace App\Domain\Service;

final class WorkTimeCalculationService
{
    public function calculateRoundedHours(\DateTimeInterface $start, \DateTimeInterface $end): float
    {
        $interval = $start->diff($end);
        $totalMinutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
        return round($totalMinutes / 30) * 0.5;
    }

    public function validateWorkTimeRange(\DateTimeInterface $start, \DateTimeInterface $end, float $maxHours = 12.0): bool
    {
        $hours = $this->calculateRoundedHours($start, $end);
        return $hours <= $maxHours && $start < $end;
    }

    public function isWithinSameDay(\DateTimeInterface $start, \DateTimeInterface $end): bool
    {
        return $start->format('Y-m-d') === $end->format('Y-m-d');
    }
}