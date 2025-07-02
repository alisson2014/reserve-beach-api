<?php

declare(strict_types=1);

namespace App\Repository\CartItemRepository;

use App\Entity\{CartItem, CourtSchedule, User};
use DateTimeImmutable;

interface ICartItemRepository
{
    public function get(int $id): ?CartItem;
    
    public function add(CartItem $cartItem, bool $flush = false): CartItem;

    public function remove(CartItem $cartItem, bool $flush = false): CartItem;

    public function removeByIds(array $ids, bool $flush = false): void;

    public function findOneByUserAndSchedule(User $user, CourtSchedule $courtSchedule, DateTimeImmutable $dateScheduled): ?CartItem;
}