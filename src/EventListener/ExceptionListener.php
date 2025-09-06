<?php

namespace App\EventListener;

use App\Domain\DTO\ErrorResponse;
use App\Domain\Exception\DomainException;
use App\Domain\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
        private bool $debug = false
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        
        // Log the exception
        $this->logException($exception);
        
        // Create appropriate response
        $response = $this->createErrorResponse($exception);
        
        $event->setResponse($response);
    }

    private function createErrorResponse(\Throwable $exception): JsonResponse
    {
        if ($exception instanceof DomainException) {
            return $this->handleDomainException($exception);
        }

        if ($exception instanceof ValidationException) {
            return $this->handleValidationException($exception);
        }

        if ($exception instanceof HttpExceptionInterface) {
            return $this->handleHttpException($exception);
        }

        return $this->handleGenericException($exception);
    }

    private function handleDomainException(DomainException $exception): JsonResponse
    {
        $errorResponse = new ErrorResponse(
            error: $exception->getMessage(),
            code: $exception->getErrorCode(),
            details: $exception->getContext()
        );

        return new JsonResponse(
            $errorResponse->toArray(),
            $exception->getStatusCode(),
            $exception->getHeaders()
        );
    }

    private function handleValidationException(ValidationException $exception): JsonResponse
    {
        $errorResponse = new ErrorResponse(
            error: $exception->getMessage(),
            code: 'VALIDATION_ERROR'
        );

        return new JsonResponse(
            $errorResponse->toArray(),
            $exception->getStatusCode()
        );
    }

    private function handleHttpException(HttpExceptionInterface $exception): JsonResponse
    {
        $errorResponse = new ErrorResponse(
            error: $exception->getMessage() ?: 'An error occurred',
            code: 'HTTP_ERROR'
        );

        return new JsonResponse(
            $errorResponse->toArray(),
            $exception->getStatusCode(),
            $exception->getHeaders()
        );
    }

    private function handleGenericException(\Throwable $exception): JsonResponse
    {
        $message = $this->debug 
            ? $exception->getMessage() 
            : 'An internal server error occurred';

        $details = $this->debug ? [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ] : null;

        $errorResponse = new ErrorResponse(
            error: $message,
            code: 'INTERNAL_ERROR',
            details: $details
        );

        return new JsonResponse(
            $errorResponse->toArray(),
            500
        );
    }

    private function logException(\Throwable $exception): void
    {
        $context = [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];

        if ($exception instanceof DomainException) {
            $context['error_code'] = $exception->getErrorCode();
            $context['domain_context'] = $exception->getContext();
        }

        $this->logger->error('Exception occurred', $context);
    }
}