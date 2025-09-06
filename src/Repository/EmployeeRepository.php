<?php

namespace App\Repository;

use App\Domain\DTO\CreateEmployeeRequest;
use App\Domain\Rule\EmployeeValidator;
use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class EmployeeRepository extends ServiceEntityRepository
{
    private EmployeeValidator $employeeValidator;

    public function __construct(
        ManagerRegistry $registry,
        EmployeeValidator $employeeValidator
    ) {
        parent::__construct($registry, Employee::class);
        $this->employeeValidator = $employeeValidator;
    }

    public function createEmployee(CreateEmployeeRequest $request): Employee
    {
        $employee = new Employee($request->firstName, $request->lastName);

        $this->getEntityManager()->persist($employee);
        $this->getEntityManager()->flush();

        return $employee;
    }
}