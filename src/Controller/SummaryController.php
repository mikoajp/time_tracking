<?php

namespace App\Controller;


use App\Domain\DTO\ErrorResponse;
use App\Domain\Exception\ValidationException;
use App\Service\SummaryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SummaryController extends AbstractController
{
    #[Route('/api/summary', methods: ['GET'])]
    public function getSummary(Request $request, SummaryService $summaryService): JsonResponse
    {
        $employeeId = $request->query->get('employeeId');
        $date = $request->query->get('date');

        if (!$employeeId || !$date) {
            $missingFields = [];
            if (!$employeeId) $missingFields[] = 'employeeId';
            if (!$date) $missingFields[] = 'date';
            
            throw ValidationException::missingFields($missingFields);
        }

        $summary = $summaryService->getSummary($employeeId, $date);
        return $this->json($summary);
    }
}