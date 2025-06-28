<?php

declare(strict_types=1);

namespace App\Repository\CourtRepository;

use App\Entity\{Court, CourtType};

interface ICourtRepository
{
    public function getAll(): array;

    public function findAll(?string $name = null, ?CourtType $courtType = null, ?bool $active = null): array;

    public function getById(int $id): ?Court;

    public function add(Court $court, bool $flush = false): Court;

    public function update(Court $court, bool $flush = false): Court;

    public function setActive(Court $court, bool $active, bool $flush = false): Court;

    public function remove(Court $court, bool $flush = false): void;
}