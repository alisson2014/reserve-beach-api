<?php

declare(strict_types=1);

namespace App\Repository\UserRepository;

use App\Entity\User;

interface IUserRepository
{
    public function getByEmail(string $email): ?User;
    public function add(User $user, bool $flush = false): User;
    public function update(User $user, bool $flush = false): User;
    public function remove(User $entity, bool $flush = false): void;
}