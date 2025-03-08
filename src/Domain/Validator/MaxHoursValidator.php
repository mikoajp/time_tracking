<?php


namespace App\Domain\Rule;

use App\Entity\WorkTime;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MaxHoursValidator
{
    private float $maxHoursPerDay;

    public function __construct(float $maxHoursPerDay = 12.0)
    {
        $this->maxHoursPerDay = $maxHoursPerDay;
    }

    public function validate(WorkTime $workTime): void
    {
        $diff = $workTime->getStart()->diff($workTime->getEnd());
        $hours = $diff->h + ($diff->i / 60);
        if ($hours > $this->maxHoursPerDay || $diff->days > 0) {
            throw new BadRequestHttpException('Work time cannot exceed 12 hours or span multiple days');
        }
    }
}