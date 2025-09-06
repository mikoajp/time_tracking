<?php

namespace App\Domain\Rule;

use App\Entity\Employee;
use App\Repository\WorkTimeRepository;
use App\Domain\Exception\ValidationException;
use App\Domain\Exception\EmployeeNotFoundException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EmployeeValidator
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
        if (empty($workTimes) && !$this->workTimeRepository->getEntityManager()->getRepository(Employee::class)->find($uuid->toBinary())) {
            throw new EmployeeNotFoundException($uuid->toRfc4122());
        }
    }

    public function validateEmployeeData(array $data): Employee
    {
        $missingFields = [];
        if (!isset($data['firstName'])) $missingFields[] = 'firstName';
        if (!isset($data['lastName'])) $missingFields[] = 'lastName';
        
        if (!empty($missingFields)) {
            throw ValidationException::missingFields($missingFields);
        }

        $employee = new Employee($data['firstName'], $data['lastName']);

        $errors = $this->validator->validate($employee);
        if (count($errors) > 0) {
            throw ValidationException::invalidValue('employee', (string) $errors);
        }

        return $employee;
    }

    public function findEmployeeOrFail(string $employeeId): Employee
    {
        $employee = $this->workTimeRepository->getEntityManager()
            ->getRepository(Employee::class)
            ->find($employeeId);

        if (!$employee) {
            throw new EmployeeNotFoundException($employeeId);
        }

        return $employee;
    }
}