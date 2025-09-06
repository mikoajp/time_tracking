<?php

namespace App\Domain\Exception;

final class WorkTimeConflictException extends DomainException
{
    public function __construct(string $date)
    {
        parent::__construct(
            message: "Work time entry already exists for date: {$date}",
            statusCode: 409
        );
    }

    public function getErrorCode(): string
    {
        return 'WORK_TIME_CONFLICT';
    }
}