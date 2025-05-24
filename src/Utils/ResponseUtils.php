<?php

declare(strict_types=1);

namespace App\Utils;

use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ResponseUtils
{
    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $field = $error->getPropertyPath();
            $errorMessages[$field][] = $error->getMessage();
        }
        return $errorMessages;
    }

    protected function notFoundResource(string $message): JsonResponse
    {
        return $this->json(['status' => true, 'message' => $message], Response::HTTP_NOT_FOUND);
    }

    protected function badRequest(ConstraintViolationListInterface $errors): JsonResponse
    {
        return $this->json(['status' => false, 'errors' => $this->formatValidationErrors($errors)], Response::HTTP_BAD_REQUEST);
    }
}