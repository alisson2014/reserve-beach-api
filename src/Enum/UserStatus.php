<?php

namespace App\Enum;

enum UserStatus: string
{
    case ACTIVE = 's';
    case INACTIVE = 'n';
}