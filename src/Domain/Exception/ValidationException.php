<?php

namespace App\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ValidationException extends BadRequestHttpException
{
    public static function missingFields(array $fields): self
    {
        return new self(sprintf('Missing required fields: %s', implode(', ', $fields)));
    }

    public static function invalidFormat(string $field, string $expectedFormat = null): self
    {
        $message = sprintf('Invalid format for field: %s', $field);
        if ($expectedFormat) {
            $message .= sprintf('. Expected: %s', $expectedFormat);
        }
        return new self($message);
    }

    public static function invalidValue(string $field, string $reason): self
    {
        return new self(sprintf('Invalid value for %s: %s', $field, $reason));
    }
}