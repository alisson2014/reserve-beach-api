<?php

declare(strict_types=1);

namespace App\Interface;

interface Arrayable
{
    /**
     * Convert the object to an array.
     *
     * @return array The object as an array.
     */
    public function toArray(): array;
}