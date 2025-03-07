<?php

namespace App\Service\Strategy;

use App\Domain\DTO\SummaryDTO;
use App\Domain\Interface\SummaryStrategyInterface;

class MonthlySummaryStrategy implements SummaryStrategyInterface
{
    private int $monthlyNorm;
    private int $baseRate;
    private float $overtimeRate;

    public function __construct(int $monthlyNorm, int $baseRate, float $overtimeMultiplier)
    {
        $this->monthlyNorm = $monthlyNorm;
        $this->baseRate = $baseRate;
        $this->overtimeRate = $baseRate * $overtimeMultiplier;
    }

    public function calculate(array $groupedHours, \DateTimeImmutable $date): SummaryDTO
    {
        $totalHours = array_sum($groupedHours);
        $standardHours = min($totalHours, $this->monthlyNorm);
        $overtimeHours = max($totalHours - $this->monthlyNorm, 0);
        $totalPay = ($standardHours * $this->baseRate) + ($overtimeHours * $this->overtimeRate);

        return new SummaryDTO(
            period: $date->format('Y-m'),
            totalHours: $totalHours,
            standardHours: $standardHours,
            overtimeHours: $overtimeHours,
            totalPay: $totalPay,
            baseRate: $this->baseRate,
            overtimeRate: $this->overtimeRate
        );
    }
}