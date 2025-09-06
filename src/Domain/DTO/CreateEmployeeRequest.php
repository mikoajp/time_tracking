<?php

namespace App\Domain\DTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly final class CreateEmployeeRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'First name is required')]
        #[Assert\Length(min: 2, max: 255)]
        public string $firstName,
        
        #[Assert\NotBlank(message: 'Last name is required')]
        #[Assert\Length(min: 2, max: 255)]
        public string $lastName
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['firstName'] ?? '',
            lastName: $data['lastName'] ?? ''
        );
    }
}