<?php

declare(strict_types=1);

namespace App\Repository\CartRepository;

use App\Entity\Cart;
use App\Enum\CartStatus;

interface ICartRepository
{
    public function getById(int $id): ?Cart;

    public function all(int $user): array;

    public function active(int $user): ?Cart;

    public function add(Cart $cart, bool $flush = false): Cart;

    public function setStatus(Cart $cart, CartStatus $status, bool $flush = false): Cart;
}