<?php

namespace App\Service;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;

class EmployeeService
{
    private EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function createEmployee(array $data): Employee
    {
        return $this->employeeRepository->createEmployee($data);
    }
}