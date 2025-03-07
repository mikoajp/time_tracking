<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\WorkTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorkTimeService
{
    private EntityManagerInterface $em;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function registerWorkTime(?string $employeeId, ?string $start, ?string $end, Employee $employee = null): WorkTime
    {
        if (empty($employeeId) || empty($start) || empty($end)) {
            throw new BadRequestHttpException('Missing required fields: employeeId, start, end');
        }

        if (!$employee) {
            $employee = $this->em->getRepository(Employee::class)->find($employeeId);
            if (!$employee) {
                throw new NotFoundHttpException('Employee not found');
            }
        }

        try {
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid date format');
        }

        if ($endDate <= $startDate) {
            throw new BadRequestHttpException('End time must be after start time');
        }

        $diff = $startDate->diff($endDate);
        $hours = $diff->h + ($diff->i / 60);
        if ($hours > 12 || $diff->days > 0) {
            throw new BadRequestHttpException('Work time cannot exceed 12 hours or span multiple days');
        }

        $workTime = new WorkTime($employee, $startDate, $endDate);
        $errors = $this->validator->validate($workTime);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $this->em->persist($workTime);
        $this->em->flush();

        return $workTime;
    }
}