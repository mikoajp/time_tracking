<?php

namespace App\Domain\Service;

use App\Domain\ValueObject\Money;

final class PayrollCalculator
{
    public function __construct(
        private int $baseRate,
        private float $overtimeMultiplier,
        private int $monthlyNorm
    ) {}

    public function calculateMonthlyPay(float $totalHours): array
    {
        $standardHours = min($totalHours, $this->monthlyNorm);
        $overtimeHours = max($totalHours - $this->monthlyNorm, 0);
        
        $standardPay = new Money($standardHours * $this->baseRate);
        $overtimePay = new Money($overtimeHours * $this->baseRate * $this->overtimeMultiplier);
        $totalPay = $standardPay->add($overtimePay);

        return [
            'standardHours' => $standardHours,
            'overtimeHours' => $overtimeHours,
            'standardPay' => $standardPay,
            'overtimePay' => $overtimePay,
            'totalPay' => $totalPay,
            'baseRate' => $this->baseRate,
            'overtimeRate' => $this->baseRate * $this->overtimeMultiplier
        ];
    }

    public function calculateDailyPay(float $hours): array
    {
        $totalPay = new Money($hours * $this->baseRate);

        return [
            'hours' => $hours,
            'totalPay' => $totalPay,
            'baseRate' => $this->baseRate
        ];
    }
}