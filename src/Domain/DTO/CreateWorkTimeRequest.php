<?php

namespace App\Domain\DTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly final class CreateWorkTimeRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Employee ID is required')]
        #[Assert\Uuid(message: 'Employee ID must be a valid UUID')]
        public string $employeeId,
        
        #[Assert\NotBlank(message: 'Start time is required')]
        #[Assert\DateTime(format: 'Y-m-d H:i', message: 'Start time must be in format YYYY-MM-DD HH:MM')]
        public string $start,
        
        #[Assert\NotBlank(message: 'End time is required')]
        #[Assert\DateTime(format: 'Y-m-d H:i', message: 'End time must be in format YYYY-MM-DD HH:MM')]
        public string $end
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            employeeId: $data['employeeId'] ?? '',
            start: $data['start'] ?? '',
            end: $data['end'] ?? ''
        );
    }
}