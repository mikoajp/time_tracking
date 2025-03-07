<?php

namespace App\Entity;

use App\Repository\WorkTimeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: WorkTimeRepository::class)]
#[UniqueEntity(fields: ['employee', 'startDay'], message: 'Pracownik może mieć tylko jeden wpis dziennie')]
class WorkTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Employee $employee;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $start;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $end;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $startDay;

    #[ORM\Column(type: 'float')]
    private float $hours;

    public function __construct(Employee $employee, \DateTimeInterface $start, \DateTimeInterface $end)
    {
        $this->employee = $employee;
        $this->start = $start;
        $this->end = $end;
        $this->startDay = (clone $start)->setTime(0, 0);
        $this->hours = $this->calculateRoundedHours($start, $end);
    }

    private function calculateRoundedHours(\DateTimeInterface $start, \DateTimeInterface $end): float
    {
        $interval = $start->diff($end);
        $totalMinutes = ($interval->h * 60) + $interval->i;
        return round($totalMinutes / 30) * 0.5;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHours(): float
    {
        return $this->hours;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee): self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getStart(): \DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): \DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;
        return $this;
    }

    public function getStartDay(): \DateTimeInterface
    {
        return $this->startDay;
    }

    public function setStartDay(\DateTimeInterface $startDay): self
    {
        $this->startDay = $startDay;
        return $this;
    }

    public function __toString(): string
    {
        return $this->startDay->format('Y-m-d');
    }
}