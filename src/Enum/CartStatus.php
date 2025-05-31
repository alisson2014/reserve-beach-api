<?php

declare(strict_types=1);

namespace App\Enum;

enum CartStatus: string
{
    case OPEN = 'op';
    case CLOSED = 'cl';
    case CANCELED = 'cc';

    public function isOpen(): bool
    {
        return $this === self::OPEN;
    }

    public function isClosed(): bool
    {
        return $this === self::CLOSED;
    }

    public function isCanceled(): bool
    {
        return $this === self::CANCELED;
    }

    public function isFinalized(): bool
    {
        return $this->isClosed() || $this->isCanceled();
    }

    public function status(): string
    {
        return match ($this) {
            self::OPEN => 'Aberto',
            self::CLOSED => 'Fechado',
            self::CANCELED => 'Cancelado',
        };
    }
}