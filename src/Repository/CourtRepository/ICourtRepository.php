<?php

declare(strict_types=1);

namespace App\Repository\CourtRepository;

use App\Entity\Court;

interface ICourtRepository
{
    public function getAll(): array;

    public function getById(int $id): ?Court;

    public function add(Court $court): Court;

    public function update(Court $court): Court;

    public function remove(Court $court): void;
}