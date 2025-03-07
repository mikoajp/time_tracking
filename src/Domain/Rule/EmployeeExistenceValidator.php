<?php

namespace App\Domain\Rule;

use App\Entity\Employee;
use App\Repository\WorkTimeRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class EmployeeExistenceValidator
{
    private WorkTimeRepository $workTimeRepository;

    public function __construct(WorkTimeRepository $workTimeRepository)
    {
        $this->workTimeRepository = $workTimeRepository;
    }


    public function check(Uuid $uuid, array $workTimes): void
    {
        if (empty($workTimes) && !$this->workTimeRepository->em->getRepository(Employee::class)->find($uuid->toBinary())) {
            throw new NotFoundHttpException('Employee not found');
        }
    }
}