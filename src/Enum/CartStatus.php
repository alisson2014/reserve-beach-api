<?php

declare(strict_types=1);

namespace App\Enum;

enum CartStatus: string
{
    case OPEN = 'op';
    case CLOSED = 'cl';
    case CANCELED = 'cc';
    case EXPIRED = 'ex';

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

    public function isExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    public function isFinalized(): bool
    {
        return $this->isClosed() || $this->isCanceled() || $this->isExpired();
    }

    public static function isValidValue(string $value): bool
    {
        return in_array($value, [self::CANCELED->value, self::CLOSED->value], true);
    }

    public function status(): string
    {
        return match ($this) {
            self::OPEN => 'Aberto',
            self::CLOSED => 'Fechado',
            self::CANCELED => 'Cancelado',
            self::EXPIRED => 'Expirado',
            default => 'Desconhecido',
        };
    }
}