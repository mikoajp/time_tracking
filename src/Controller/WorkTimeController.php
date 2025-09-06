<?php

namespace App\Controller;

use App\Domain\DTO\CreateWorkTimeRequest;
use App\Domain\DTO\ErrorResponse;
use App\Domain\Exception\ValidationException;
use App\Service\WorkTimeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorkTimeController extends AbstractController
{
    #[Route('/api/work-times', methods: ['POST'])]
    public function register(Request $request, WorkTimeService $workTimeService, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!is_array($data)) {
            $errorResponse = new ErrorResponse('Invalid JSON payload', code: 'INVALID_JSON');
            return new JsonResponse($errorResponse->toArray(), 400);
        }

        $createRequest = CreateWorkTimeRequest::fromArray($data);
        
        $errors = $validator->validate($createRequest);
        if (count($errors) > 0) {
            throw ValidationException::invalidValue('request', (string) $errors);
        }

        $workTime = $workTimeService->registerWorkTime(
            $createRequest->employeeId,
            $createRequest->start,
            $createRequest->end
        );
        
        return $this->json(['message' => 'Czas pracy został dodany!'], 201);
    }
}