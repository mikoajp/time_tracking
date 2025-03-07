<?php

namespace App\Controller;


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

        try {
            $summary = $summaryService->getSummary($employeeId, $date);
            return $this->json($summary);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}