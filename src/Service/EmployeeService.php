<?php

namespace App\Service;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmployeeService
{
    private EntityManagerInterface $em;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function createEmployee(array $data): Employee
    {
        if (!isset($data['firstName']) || !isset($data['lastName'])) {
            throw new BadRequestHttpException('Missing required fields: firstName, lastName');
        }

        $employee = new Employee($data['firstName'], $data['lastName']);

        $errors = $this->validator->validate($employee);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $this->em->persist($employee);
        $this->em->flush();

        return $employee;
    }
}