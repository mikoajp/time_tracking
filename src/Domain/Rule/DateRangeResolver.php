<?php

namespace App\Domain\Rule;

use DateTimeImmutable;

final class DateRangeResolver
{

    /**
     * @throws \DateMalformedStringException
     */
    public function resolve(string $date, bool $isMonth): array
    {
        $dateObj = new DateTimeImmutable($date);

        if ($isMonth) {
            $startDate = $dateObj->modify('first day of this month 00:00:00');
            $endDate = $dateObj->modify('last day of this month 23:59:59');
        } else {
            $startDate = $dateObj->setTime(0, 0, 0);
            $endDate = $dateObj->setTime(23, 59, 59);
        }

        return [$dateObj, $startDate, $endDate];
    }
}