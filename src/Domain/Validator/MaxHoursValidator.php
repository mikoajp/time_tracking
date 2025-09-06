<?php


namespace App\Domain\Validator;

use App\Domain\Service\WorkTimeCalculationService;
use App\Entity\WorkTime;
use App\Domain\Exception\InvalidWorkTimeException;

final class MaxHoursValidator
{
    public function __construct(
        private WorkTimeCalculationService $calculationService,
        private float $maxHoursPerDay = 12.0
    ) {}

    public function validate(WorkTime $workTime): void
    {
        if (!$this->calculationService->validateWorkTimeRange($workTime->getStart(), $workTime->getEnd(), $this->maxHoursPerDay)) {
            throw new InvalidWorkTimeException(
                'Work time cannot exceed 12 hours',
                [
                    'max_hours' => $this->maxHoursPerDay,
                    'actual_hours' => $this->calculationService->calculateRoundedHours($workTime->getStart(), $workTime->getEnd())
                ]
            );
        }
        
        if (!$this->calculationService->isWithinSameDay($workTime->getStart(), $workTime->getEnd())) {
            throw new InvalidWorkTimeException(
                'Work time cannot span multiple days',
                [
                    'start_date' => $workTime->getStart()->format('Y-m-d'),
                    'end_date' => $workTime->getEnd()->format('Y-m-d')
                ]
            );
        }
    }
}