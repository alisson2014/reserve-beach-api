<?php

declare(strict_types=1);

namespace App\Repository\CourtTypeRepository;

use App\Entity\CourtType;

interface ICourtTypeRepository
{
    /** @return CourtType[] */
    public function getByNameLike(string $name): array;

    /** @return CourtType[] */
    public function getAll(): array;

    public function getById(int $id): ?CourtType;

    public function add(CourtType $courtType, bool $flush = false): CourtType;

    public function update(CourtType $courtType, bool $flush = false): CourtType;

    public function remove(CourtType $entity, bool $flush = false): void;
}