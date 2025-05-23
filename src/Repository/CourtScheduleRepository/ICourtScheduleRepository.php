<?php

declare(strict_types=1);

namespace App\Repository\CourtScheduleRepository;

use App\Entity\CourtSchedule;

interface ICourtScheduleRepository
{
    /** @return CourtSchedule[] */
    public function getAll(?int $courtId = null): array;

    public function getById(int $id): ?CourtSchedule;

    public function add(CourtSchedule $courtSchedule, bool $flush = false): CourtSchedule;

    public function remove(CourtSchedule $courtSchedule, bool $flush = false): void;
}