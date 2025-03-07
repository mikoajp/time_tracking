<?php

namespace App\Controller;

use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class EmployeeController extends AbstractController
{
    #[Route('/api/employees', methods: ['POST'])]
    public function create(Request $request, EmployeeService $employeeService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $employee = $employeeService->createEmployee($data);
            return $this->json(['id' => $employee->getId()], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}