<?php

namespace App\Domain\Rule;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DateRangeResolver
{
    public function resolve(string $date, bool $isMonth): array
    {
        try {
            $dateObj = new \DateTimeImmutable($date, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid date value: ' . $e->getMessage());
        }

        if ($isMonth) {
            $startDate = $dateObj->modify('first day of this month');
            $endDate = $dateObj->modify('last day of this month');
        } else {
            $startDate = $dateObj;
            $endDate = $dateObj;
        }

        return [$dateObj, $startDate, $endDate];
    }
}