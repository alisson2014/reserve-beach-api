<?php

declare(strict_types=1);

namespace App\Dto;

interface Dto
{
    public static function fromArray(array $data): self;
}