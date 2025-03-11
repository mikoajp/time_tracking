<?php

namespace App\Domain\Rule;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Uid\Uuid;

class SummaryRequestValidator
{
    public function validate(string $employeeId, string $date): array
    {
        if (!Uuid::isValid($employeeId)) {
            throw new BadRequestHttpException('Invalid employee ID format');
        }

        $isMonth = preg_match('/^\d{4}-\d{2}$/', $date);
        $isDay = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
        if (!$isMonth && !$isDay) {
            throw new BadRequestHttpException('Invalid date format. Expected YYYY-MM or YYYY-MM-DD');
        }

        $uuid = Uuid::fromString($employeeId);
        return [$uuid, $isMonth];
    }
}