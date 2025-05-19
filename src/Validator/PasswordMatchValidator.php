<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordMatchValidator extends ConstraintValidator
{
    public function validate($object, Constraint $constraint)
    {
        if (!$object->password || !$object->confirmPassword) {
            return;
        }

        if ($object->password !== $object->confirmPassword) {
            $this->context->buildViolation($constraint->message)
                ->atPath('confirmPassword')
                ->addViolation();
        }
    }
}