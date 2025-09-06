<?php

namespace App\Domain\DTO;

readonly final class ErrorResponse
{
    public function __construct(
        public string $error,
        public ?array $details = null,
        public ?string $code = null
    ) {}

    public function toArray(): array
    {
        $response = ['error' => $this->error];
        
        if ($this->details !== null) {
            $response['details'] = $this->details;
        }
        
        if ($this->code !== null) {
            $response['code'] = $this->code;
        }
        
        return $response;
    }
}