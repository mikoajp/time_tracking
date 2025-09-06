<?php


namespace App\Domain\Validator;

use App\Entity\WorkTime;
use App\Repository\WorkTimeRepository;
use App\Domain\Exception\WorkTimeConflictException;

final class UniqueDayValidator
{
    private WorkTimeRepository $repository;

    public function __construct(WorkTimeRepository $repository)
    {
        $this->repository = $repository;
    }


    public function validate(WorkTime $workTime): void
    {
        $existing = $this->repository->findBy([
            'employee' => $workTime->getEmployee(),
            'startDay' => $workTime->getStartDay(),
        ]);

        if (!empty($existing) && ($workTime->getId() === null || $existing[0]->getId() !== $workTime->getId())) {
            throw new WorkTimeConflictException($workTime->getStartDay()->format('Y-m-d'));
        }
    }
}