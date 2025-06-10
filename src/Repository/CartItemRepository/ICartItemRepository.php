<?php

declare(strict_types=1);

namespace App\Repository\CartItemRepository;

use App\Entity\CartItem;
use App\Entity\CourtSchedule;
use App\Entity\User;

interface ICartItemRepository
{
    public function add(CartItem $cartItem, bool $flush = false): CartItem;

    public function remove(CartItem $cartItem, bool $flush = false): CartItem;

    public function findOneByUserAndSchedule(User $user, CourtSchedule $courtSchedule): ?CartItem;
}