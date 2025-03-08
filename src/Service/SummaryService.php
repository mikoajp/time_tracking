<?php

namespace App\Service;

use App\Domain\Rule\DateRangeResolver;
use App\Infra\HoursProcessor;
use App\Repository\WorkTimeRepository;
use App\Service\Strategy\MonthlySummaryStrategy;
use App\Service\Strategy\DailySummaryStrategy;
use App\Domain\Rule\SummaryRequestValidator;
use App\Domain\Rule\EmployeeValidator;

class SummaryService
{
    private SummaryRequestValidator $validator;
    private WorkTimeRepository $workTimeRepository;
    private HoursProcessor $hoursProcessor;
    private EmployeeValidator $employeeChecker;
    private DateRangeResolver $dateRangeResolver;
    private MonthlySummaryStrategy $monthlyStrategy;
    private DailySummaryStrategy $dailyStrategy;

    public function __construct(
        WorkTimeRepository $workTimeRepository,
        HoursProcessor $hoursProcessor,
        MonthlySummaryStrategy $monthlyStrategy,
        DailySummaryStrategy $dailyStrategy,
        SummaryRequestValidator $validator,
        EmployeeValidator $employeeChecker,
        DateRangeResolver $dateRangeResolver
    ) {
        $this->workTimeRepository = $workTimeRepository;
        $this->hoursProcessor = $hoursProcessor;
        $this->monthlyStrategy = $monthlyStrategy;
        $this->dailyStrategy = $dailyStrategy;
        $this->validator = $validator;
        $this->employeeChecker = $employeeChecker;
        $this->dateRangeResolver = $dateRangeResolver;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function getSummary(string $employeeId, string $date): array
    {
        [$uuid, $isMonth] = $this->validator->validate($employeeId, $date);

        [$dateObj, $startDate, $endDate] = $this->dateRangeResolver->resolve($date, $isMonth);

        $workTimes = $isMonth
            ? $this->workTimeRepository->findByEmployeeAndPeriod($uuid, $startDate, $endDate)
            : $this->workTimeRepository->findByEmployeeAndDay($uuid, $dateObj);

        $this->employeeChecker->check($uuid, $workTimes);

        $groupedHours = $this->hoursProcessor->processWorkTimes($workTimes);
        $strategy = $isMonth ? $this->monthlyStrategy : $this->dailyStrategy;

        return $strategy->calculate($groupedHours, $dateObj)->toArray();
    }
}