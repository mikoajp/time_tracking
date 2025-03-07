<?php

namespace App\Service;

use App\Infra\HoursProcessor;
use App\Domain\Rule\SummaryRequestValidator;
use App\Domain\Rule\DateRangeResolver;
use App\Domain\Rule\EmployeeExistenceValidator;
use App\Repository\WorkTimeRepository;
use App\Service\Strategy\SummaryStrategyResolver;

class SummaryService
{
    private WorkTimeRepository $workTimeRepository;
    private HoursProcessor $hoursProcessor;
    private SummaryRequestValidator $validator;
    private DateRangeResolver $dateRangeResolver;
    private EmployeeExistenceValidator $employeeChecker;
    private SummaryStrategyResolver $strategyResolver;

    public function __construct(
        WorkTimeRepository         $workTimeRepository,
        HoursProcessor             $hoursProcessor,
        SummaryRequestValidator    $validator,
        DateRangeResolver          $dateRangeResolver,
        EmployeeExistenceValidator $employeeChecker,
        SummaryStrategyResolver    $strategyResolver
    ) {
        $this->workTimeRepository = $workTimeRepository;
        $this->hoursProcessor = $hoursProcessor;
        $this->validator = $validator;
        $this->dateRangeResolver = $dateRangeResolver;
        $this->employeeChecker = $employeeChecker;
        $this->strategyResolver = $strategyResolver;
    }

    public function getSummary(string $employeeId, string $date): array
    {
        [$uuid, $isMonth] = $this->validator->validate($employeeId, $date);

        [$dateObj, $startDate, $endDate] = $this->dateRangeResolver->resolve($date, $isMonth);

        $workTimes = $isMonth
            ? $this->workTimeRepository->findByEmployeeAndPeriod($uuid, $startDate, $endDate)
            : $this->workTimeRepository->findByEmployeeAndDay($uuid, $dateObj);

        $this->employeeChecker->check($uuid, $workTimes);

        $groupedHours = $this->hoursProcessor->processWorkTimes($workTimes);
        $strategy = $this->strategyResolver->resolve($isMonth);

        return $strategy->calculate($groupedHours, $dateObj)->toArray();
    }
}