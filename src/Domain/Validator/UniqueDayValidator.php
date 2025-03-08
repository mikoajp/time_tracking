<?php


namespace App\Domain\Rule;

use App\Entity\WorkTime;
use App\Repository\WorkTimeRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UniqueDayValidator
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
            throw new BadRequestHttpException('An employee may have only one daily entry');
        }
    }
}