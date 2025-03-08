<?php

namespace App\Domain\Validator;

use App\Domain\Rule\MaxHoursValidator;
use App\Domain\Rule\UniqueDayValidator;
use App\Entity\Employee;
use App\Entity\WorkTime;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorkTimeValidator
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
        if (empty($employeeId) || empty($start) || empty($end)) {
            throw new BadRequestHttpException('Missing required fields: employeeId, start, end');
        }

        try {
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid date format');
        }

        if ($endDate <= $startDate) {
            throw new BadRequestHttpException('End time must be after start time');
        }

        $workTime = new WorkTime($employee, $startDate, $endDate);

        $errors = $this->validator->validate($workTime);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $this->uniqueDayValidator->validate($workTime);
        $this->maxHoursValidator->validate($workTime);

        return $workTime;
    }
}