<?php

namespace App\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class DomainException extends HttpException
{
    public function __construct(
        string $message = '',
        int $statusCode = 400,
        ?\Throwable $previous = null,
        array $headers = []
    ) {
        parent::__construct($statusCode, $message, $previous, $headers);
    }

    abstract public function getErrorCode(): string;
    
    public function getContext(): array
    {
        return [];
    }
}