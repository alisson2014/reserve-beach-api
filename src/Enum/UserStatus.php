<?php

declare(strict_types=1);

namespace App\Enum;

enum UserStatus: string
{
    case ACTIVE = 's';
    case INACTIVE = 'n';
}