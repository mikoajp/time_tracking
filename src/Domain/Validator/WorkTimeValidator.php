<?php

namespace App\Domain\Validator;

use App\Domain\Validator\MaxHoursValidator;
use App\Domain\Validator\UniqueDayValidator;
use App\Entity\Employee;
use App\Entity\WorkTime;
use App\Domain\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class WorkTimeValidator
{
    private ValidatorInterface $validator;
    private UniqueDayValidator $uniqueDayValidator;
    private MaxHoursValidator $maxHoursValidator;

    public function __construct(
        ValidatorInterface $validator,
        UniqueDayValidator $uniqueDayValidator,
        MaxHoursValidator $maxHoursValidator
    ) {
        $this->validator = $validator;
        $this->uniqueDayValidator = $uniqueDayValidator;
        $this->maxHoursValidator = $maxHoursValidator;
    }

    public function validateWorkTimeData(?string $employeeId, ?string $start, ?string $end, Employee $employee = null): WorkTime
    {
        $missingFields = [];
        if (empty($employeeId)) $missingFields[] = 'employeeId';
        if (empty($start)) $missingFields[] = 'start';
        if (empty($end)) $missingFields[] = 'end';
        
        if (!empty($missingFields)) {
            throw ValidationException::missingFields($missingFields);
        }

        try {
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
        } catch (\Exception $e) {
            throw ValidationException::invalidFormat('date', 'YYYY-MM-DD HH:MM');
        }

        if ($endDate <= $startDate) {
            throw ValidationException::invalidValue('end', 'End time must be after start time');
        }

        $workTime = new WorkTime($employee, $startDate, $endDate);

        $errors = $this->validator->validate($workTime);
        if (count($errors) > 0) {
            throw ValidationException::invalidValue('workTime', (string) $errors);
        }

        $this->uniqueDayValidator->validate($workTime);
        $this->maxHoursValidator->validate($workTime);

        return $workTime;
    }
}