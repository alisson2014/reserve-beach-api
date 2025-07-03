<?php

declare(strict_types=1);

namespace App\Repository\ScheduleRepository;

use App\Entity\Schedule;

interface IScheduleRepository {
    public function add(Schedule $schedule, bool $flush = false): Schedule;

    public function getEntityManager(): \Doctrine\ORM\EntityManagerInterface;
}