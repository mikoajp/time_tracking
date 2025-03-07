<?php

namespace App\Repository;

use App\Entity\WorkTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

class WorkTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkTime::class);
    }

    public function findByEmployeeAndPeriod(Uuid $employeeId, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array
    {
        return $this->createQueryBuilder('wt')
            ->where('wt.employee = :employee')
            ->andWhere('wt.startDay BETWEEN :start AND :end')
            ->setParameter('employee', $employeeId->toBinary())
            ->setParameter('start', $startDate->format('Y-m-d'))
            ->setParameter('end', $endDate->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function findByEmployeeAndDay(Uuid $employeeId, DateTimeImmutable $date): array
    {
        return $this->createQueryBuilder('wt')
            ->where('wt.employee = :employee')
            ->andWhere('wt.startDay = :day')
            ->setParameter('employee', $employeeId->toBinary())
            ->setParameter('day', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }
}