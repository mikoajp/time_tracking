<?php

namespace App\Infra;

use App\Entity\WorkTime;

class HoursProcessor
{
    public function processWorkTimes(array $workTimes): array
    {
        $result = [];
        foreach ($workTimes as $workTime) {
            $day = $workTime->getStartDay()->format('Y-m-d');
            $hours = $this->calculateRoundedHours($workTime);
            $result[$day] = ($result[$day] ?? 0) + $hours;
        }
        ksort($result);
        return $result;
    }

    private function calculateRoundedHours(WorkTime $workTime): float
    {
        $minutes = ($workTime->getEnd()->getTimestamp() - $workTime->getStart()->getTimestamp()) / 60;
        return round($minutes / 30) * 0.5;
    }
}