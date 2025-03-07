<?php

namespace App\Service\Strategy;

use App\Domain\DTO\SummaryDTO;
use App\Domain\Interface\SummaryStrategyInterface;

class DailySummaryStrategy implements SummaryStrategyInterface
{
    private int $baseRate;

    public function __construct(int $baseRate)
    {
        $this->baseRate = $baseRate;
    }

    public function calculate(array $groupedHours, \DateTimeImmutable $date): SummaryDTO
    {
        $targetDay = $date->format('Y-m-d');
        $dayHours = $groupedHours[$targetDay] ?? 0;
        $totalPay = $dayHours * $this->baseRate;

        return new SummaryDTO(
            period: $targetDay,
            totalHours: $dayHours,
            standardHours: $dayHours,
            overtimeHours: 0,
            totalPay: $totalPay,
            baseRate: $this->baseRate,
            overtimeRate: null
        );
    }
}