<?php

namespace App\Repository;

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

    public function createEmployee(array $data): Employee
    {
        $employee = $this->employeeValidator->validateEmployeeData($data);

        $this->getEntityManager()->persist($employee);
        $this->getEntityManager()->flush();

        return $employee;
    }
}