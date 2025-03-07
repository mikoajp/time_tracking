<?php

namespace App\Domain\Interface;

use App\Domain\DTO\SummaryDTO;

interface SummaryStrategyInterface
{
    public function calculate(array $groupedHours, \DateTimeImmutable $date): SummaryDTO;
}