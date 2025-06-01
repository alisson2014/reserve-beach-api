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
        return $this->json(['status' => true, ...compact('message')], Response::HTTP_NOT_FOUND);
    }

    protected function badRequest(ConstraintViolationListInterface $errors): JsonResponse
    {
        return $this->json(['status' => false, 'errors' => $this->formatValidationErrors($errors)], Response::HTTP_BAD_REQUEST);
    }

    protected function internalServerError(string $message): JsonResponse
    {
        return $this->json(['status' => false, ...compact('message')], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function forbidden(string $message): JsonResponse
    {
        return $this->json(['status' => false, ...compact('message')], Response::HTTP_FORBIDDEN);
    }

    protected function ok(array|string $data, ?string $message = null): JsonResponse
    {
        return $this->json(
            [
                'status' => true,
                ...compact(['data', 'message']),
            ], 
            Response::HTTP_OK
        );
    }

    protected function created(array $data, string $message): JsonResponse
    {
        return $this->json(
            [
                'status' => true,
                ...compact(['data', 'message']),
            ], 
            Response::HTTP_CREATED
        );
    }

    public function noContent(): JsonResponse
    {
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    protected function conflict(string $message): JsonResponse
    {
        return $this->json(['status' => false, ...compact('message')], Response::HTTP_CONFLICT);
    }

    protected function unauthorized(string $message = 'Credenciais inválidas.'): JsonResponse
    {
        return $this->json(['status' => false, ...compact('message')], Response::HTTP_UNAUTHORIZED);
    }
}