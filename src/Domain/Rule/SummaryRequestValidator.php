<?php

namespace App\Domain\Rule;

use App\Domain\Exception\ValidationException;
use Symfony\Component\Uid\Uuid;

final class SummaryRequestValidator
{
    public function validate(string $employeeId, string $date): array
    {
        if (!Uuid::isValid($employeeId)) {
            throw ValidationException::invalidFormat('employeeId', 'Valid UUID format');
        }

        $isMonth = preg_match('/^\\d{4}-\\d{2}$/', $date);
        $isDay = preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $date);
        if (!$isMonth && !$isDay) {
            throw ValidationException::invalidFormat('date', 'YYYY-MM or YYYY-MM-DD');
        }

        $uuid = Uuid::fromString($employeeId);
        return [$uuid, $isMonth];
    }
}