<?php

namespace App\Service\Strategy;

use App\Domain\DTO\SummaryDTO;
use App\Domain\Interface\SummaryStrategyInterface;
use App\Domain\Service\PayrollCalculator;

final class DailySummaryStrategy implements SummaryStrategyInterface
{
    public function __construct(
        private PayrollCalculator $payrollCalculator
    ) {}

    public function calculate(array $groupedHours, \DateTimeImmutable $date): SummaryDTO
    {
        $targetDay = $date->format('Y-m-d');
        $dayHours = $groupedHours[$targetDay] ?? 0;
        $payroll = $this->payrollCalculator->calculateDailyPay($dayHours);

        return new SummaryDTO(
            period: $targetDay,
            totalHours: $dayHours,
            standardHours: $dayHours,
            overtimeHours: 0,
            totalPay: $payroll['totalPay']->getAmount(),
            baseRate: $payroll['baseRate'],
            overtimeRate: null
        );
    }
}