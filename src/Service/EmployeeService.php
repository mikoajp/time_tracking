<?php

namespace App\Service;

use App\Domain\DTO\CreateEmployeeRequest;
use App\Entity\Employee;
use App\Repository\EmployeeRepository;

class EmployeeService
{
    private EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function createEmployee(CreateEmployeeRequest $request): Employee
    {
        return $this->employeeRepository->createEmployee($request);
    }
}