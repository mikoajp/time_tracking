<?php

namespace App\Service\Strategy;

use App\Domain\DTO\SummaryDTO;
use App\Domain\Interface\SummaryStrategyInterface;
use App\Domain\Service\PayrollCalculator;

final class MonthlySummaryStrategy implements SummaryStrategyInterface
{
    public function __construct(
        private PayrollCalculator $payrollCalculator
    ) {}

    public function calculate(array $groupedHours, \DateTimeImmutable $date): SummaryDTO
    {
        $totalHours = array_sum($groupedHours);
        $payroll = $this->payrollCalculator->calculateMonthlyPay($totalHours);

        return new SummaryDTO(
            period: $date->format('Y-m'),
            totalHours: $totalHours,
            standardHours: $payroll['standardHours'],
            overtimeHours: $payroll['overtimeHours'],
            totalPay: $payroll['totalPay']->getAmount(),
            baseRate: $payroll['baseRate'],
            overtimeRate: $payroll['overtimeRate']
        );
    }
}