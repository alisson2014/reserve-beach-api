<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class PasswordMatch extends Constraint
{
    public string $message = 'A senha e a confirmação de senha devem ser iguais.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}