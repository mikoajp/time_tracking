<?php

namespace App\Domain\Exception;

final class InvalidWorkTimeException extends DomainException
{
    public function __construct(string $reason, array $context = [])
    {
        parent::__construct(
            message: "Invalid work time: {$reason}",
            statusCode: 400
        );
        $this->context = $context;
    }

    public function getErrorCode(): string
    {
        return 'INVALID_WORK_TIME';
    }

    private array $context = [];

    public function getContext(): array
    {
        return $this->context;
    }
}