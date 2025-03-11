<?php

namespace App\Service;

use App\Domain\Rule\EmployeeValidator;
use App\Domain\Validator\WorkTimeValidator;
use App\Entity\Employee;
use App\Entity\WorkTime;
use App\Repository\WorkTimeRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkTimeService
{
    private WorkTimeRepository $workTimeRepository;
    private WorkTimeValidator $workTimeValidator;
    private EntityManagerInterface $em;
    private EmployeeValidator $employeeValidator;

    public function __construct(
        WorkTimeRepository $workTimeRepository,
        WorkTimeValidator $workTimeValidator,
        EntityManagerInterface $em,
        EmployeeValidator $employeeValidator
    ) {
        $this->workTimeRepository = $workTimeRepository;
        $this->workTimeValidator = $workTimeValidator;
        $this->em = $em;
        $this->employeeValidator = $employeeValidator;
    }

    public function registerWorkTime(?string $employeeId, ?string $start, ?string $end, Employee $employee = null): WorkTime
    {
        if (!$employee) {
            $employee = $this->employeeValidator->findEmployeeOrFail($employeeId ?? '');
        }

        $workTime = $this->workTimeValidator->validateWorkTimeData($employeeId, $start, $end, $employee);
        $this->workTimeRepository->save($workTime);

        return $workTime;
    }
}