<?php

declare(strict_types=1);

namespace App\Enum;

enum Position: string
{
    case MANAGER = 'm';
    case EMPLOYEE = 'e';
}