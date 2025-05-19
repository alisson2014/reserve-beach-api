<?php

declare(strict_types=1);

namespace App\Utils;

use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ValidationErrorFormatterTrait
{
    protected function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $field = $error->getPropertyPath();
            $errorMessages[$field][] = $error->getMessage();
        }
        return $errorMessages;
    }
}