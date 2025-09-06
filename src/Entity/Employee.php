<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'First name cannot be blank')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'First name must be at least 2 characters', maxMessage: 'First name cannot exceed 255 characters')]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Last name cannot be blank')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Last name must be at least 2 characters', maxMessage: 'Last name cannot exceed 255 characters')]
    private string $lastName;

    public function __construct(string $firstName, string $lastName)
    {
        $this->id = Uuid::v4();
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId(): string
    {
        return $this->id->toRfc4122();
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }
}