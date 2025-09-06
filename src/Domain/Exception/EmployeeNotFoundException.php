<?php

namespace App\Domain\Exception;

final class EmployeeNotFoundException extends DomainException
{
    public function __construct(string $employeeId)
    {
        parent::__construct(
            message: "Employee with ID '{$employeeId}' not found",
            statusCode: 404
        );
    }

    public function getErrorCode(): string
    {
        return 'EMPLOYEE_NOT_FOUND';
    }
}