<?php

namespace App\Controller;

use App\Service\WorkTimeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class WorkTimeController extends AbstractController
{
    #[Route('/api/work-times', methods: ['POST'])]
    public function register(Request $request, WorkTimeService $workTimeService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $workTime = $workTimeService->registerWorkTime(
                $data['employeeId'] ?? null,
                $data['start'] ?? null,
                $data['end'] ?? null
            );
            return $this->json(['message' => 'Czas pracy został dodany!'], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}