<?php

namespace App\Infra;

use App\Entity\WorkTime;

final class HoursProcessor
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
        return $workTime->getHours(); // Use the already calculated hours from the entity
    }
}