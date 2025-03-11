<?php

namespace App\Service\Strategy;

use App\Domain\Interface\SummaryStrategyInterface;

class SummaryStrategyResolver
{
    private SummaryStrategyInterface $monthlyStrategy;
    private SummaryStrategyInterface $dailyStrategy;

    public function __construct(MonthlySummaryStrategy $monthlyStrategy, DailySummaryStrategy $dailyStrategy)
    {
        $this->monthlyStrategy = $monthlyStrategy;
        $this->dailyStrategy = $dailyStrategy;
    }

    public function resolve(bool $isMonth): SummaryStrategyInterface
    {
        return $isMonth ? $this->monthlyStrategy : $this->dailyStrategy;
    }
}