<?php

namespace App\Controller;

use App\Domain\DTO\CreateEmployeeRequest;
use App\Domain\DTO\ErrorResponse;
use App\Domain\Exception\ValidationException;
use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmployeeController extends AbstractController
{
    #[Route('/api/employees', methods: ['POST'])]
    public function create(Request $request, EmployeeService $employeeService, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!is_array($data)) {
            $errorResponse = new ErrorResponse('Invalid JSON payload', code: 'INVALID_JSON');
            return new JsonResponse($errorResponse->toArray(), 400);
        }

        $createRequest = CreateEmployeeRequest::fromArray($data);
        
        $errors = $validator->validate($createRequest);
        if (count($errors) > 0) {
            throw ValidationException::invalidValue('request', (string) $errors);
        }
        
        $employee = $employeeService->createEmployee($createRequest);
        return $this->json(['id' => $employee->getId()], 201);
    }
}