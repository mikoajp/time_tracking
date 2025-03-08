<?php

namespace App\Domain\Rule;

use App\Entity\Employee;
use App\Repository\WorkTimeRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmployeeValidator
{
    private WorkTimeRepository $workTimeRepository;
    private ValidatorInterface $validator;

    public function __construct(
        WorkTimeRepository $workTimeRepository,
        ValidatorInterface $validator
    ) {
        $this->workTimeRepository = $workTimeRepository;
        $this->validator = $validator;
    }

    public function check(Uuid $uuid, array $workTimes): void
    {
        if (empty($workTimes) && !$this->workTimeRepository->em->getRepository(Employee::class)->find($uuid->toBinary())) {
            throw new NotFoundHttpException('Employee not found');
        }
    }

    public function validateEmployeeData(array $data): Employee
    {
        if (!isset($data['firstName']) || !isset($data['lastName'])) {
            throw new BadRequestHttpException('Missing required fields: firstName, lastName');
        }

        $employee = new Employee($data['firstName'], $data['lastName']);

        $errors = $this->validator->validate($employee);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        return $employee;
    }

    public function findEmployeeOrFail(string $employeeId): Employee
    {
        $employee = $this->workTimeRepository->getEntityManager()
            ->getRepository(Employee::class)
            ->find($employeeId);

        if (!$employee) {
            throw new NotFoundHttpException('Employee not found');
        }

        return $employee;
    }
}