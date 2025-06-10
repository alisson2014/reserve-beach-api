<?php

declare(strict_types=1);

namespace App\Repository\CourtScheduleRepository;

use App\Entity\CourtSchedule;

interface ICourtScheduleRepository
{
    /** @return CourtSchedule[] */
    public function getAll(?int $courtId = null, ?int $dayOfWeek = null): array;

    public function getById(int $id): ?CourtSchedule;

    public function getByIds(array $ids): array;    

    public function add(CourtSchedule $courtSchedule, bool $flush = false): CourtSchedule;

    public function remove(CourtSchedule $courtSchedule, bool $flush = false): void;

    public function flush(): void;

    public function findOneByCourtWeekdayTime(int $courtId, int $dayOfWeek, string $time): CourtSchedule|null;
}