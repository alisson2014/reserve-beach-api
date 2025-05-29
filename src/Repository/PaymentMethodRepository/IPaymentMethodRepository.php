<?php

declare(strict_types=1);

namespace App\Repository\PaymentMethodRepository;

use App\Entity\PaymentMethod;

interface IPaymentMethodRepository
{
    /** @return PaymentMethod[] */
    public function getAll(?string $name = null, ?bool $active = true): array;

    public function getById(int $id): ?PaymentMethod;

    public function enable(array $ids): void;

    public function disable(array $ids): void;
}