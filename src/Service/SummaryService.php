<?php

namespace App\Service;

use App\Domain\Interface\SummaryStrategyInterface;
use App\Entity\Employee;
use App\Infra\HoursProcessor;
use App\Repository\WorkTimeRepository;
use App\Service\Strategy\DailySummaryStrategy;
use App\Service\Strategy\MonthlySummaryStrategy;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class SummaryService
{
    private WorkTimeRepository $workTimeRepository;
    private HoursProcessor $hoursProcessor;
    private SummaryStrategyInterface $monthlyStrategy;
    private SummaryStrategyInterface $dailyStrategy;

    public function __construct(
        WorkTimeRepository $workTimeRepository,
        HoursProcessor $hoursProcessor,
        MonthlySummaryStrategy $monthlyStrategy,
        DailySummaryStrategy $dailyStrategy
    ) {
        $this->workTimeRepository = $workTimeRepository;
        $this->hoursProcessor = $hoursProcessor;
        $this->monthlyStrategy = $monthlyStrategy;
        $this->dailyStrategy = $dailyStrategy;
    }

    public function getSummary(string $employeeId, string $date): array
    {
        if (!Uuid::isValid($employeeId)) {
            throw new BadRequestHttpException('Invalid employee ID format');
        }

        $isMonth = preg_match('/^\d{4}-\d{2}$/', $date);
        $isDay = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
        if (!$isMonth && !$isDay) {
            throw new BadRequestHttpException('Invalid date format. Expected YYYY-MM or YYYY-MM-DD');
        }

        $uuid = Uuid::fromString($employeeId);
        try {
            $dateObj = new \DateTimeImmutable($date, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid date value: ' . $e->getMessage());
        }

        $workTimes = $isMonth
            ? $this->workTimeRepository->findByEmployeeAndPeriod(
                $uuid,
                $dateObj->modify('first day of this month'),
                $dateObj->modify('last day of this month')
            )
            : $this->workTimeRepository->findByEmployeeAndDay($uuid, $dateObj);

        if (empty($workTimes) && !$this->workTimeRepository->em->getRepository(Employee::class)->find($uuid->toBinary())) {
            throw new NotFoundHttpException('Employee not found');
        }

        $groupedHours = $this->hoursProcessor->processWorkTimes($workTimes);
        $strategy = $isMonth ? $this->monthlyStrategy : $this->dailyStrategy;

        return $strategy->calculate($groupedHours, $dateObj)->toArray();
    }
}