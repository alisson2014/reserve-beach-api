<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\CourtScheduleDto;
use App\Interface\Arrayable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Override;

/**
 * Mapeia os horários de funcionamento de uma quadra.
 * Cada registro representa um slot de 1 hora em que a quadra está disponível.
 */
#[ORM\Entity]
#[ORM\Table(name: "court_schedules")]
#[ORM\UniqueConstraint(name: 'unique_schedule_idx', columns: ['court_id', 'day_of_week', 'start_time'])]
class CourtSchedule implements Arrayable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    /**
     * O dia da semana em que este horário é válido.
     * Convenção: 1=Domingo, 2=Segunda, ..., 7=Sábado
     */
    #[ORM\Column(type: Types::INTEGER)]
    private int $dayOfWeek;

    /**
     * A hora de início do slot de agendamento.
     * Ex: 08:00:00, 19:00:00
     */
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private \DateTime $startTime;

    /**
     * A hora de término do slot de agendamento.
     * Ex: 09:00:00, 20:00:00
     */
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private \DateTime $endTime;

    /**
     * A quadra à qual este horário pertence.
     */
    #[ORM\ManyToOne(targetEntity: Court::class, inversedBy: 'schedules')]
    #[ORM\JoinColumn(name: 'court_id', referencedColumnName: 'id', nullable: false)]
    private ?Court $court = null;

    /**
     * Coleção de agendamentos associados a este horário.
     * Usado para verificar se o horário já está ocupado.
     */
    #[ORM\OneToMany(mappedBy: 'courtSchedule', targetEntity: Schedule::class)]
    #[ORM\JoinColumn(name: 'court_schedule_id', referencedColumnName: 'id', nullable: true)]
    private Collection $schedules;

    public static function get(CourtScheduleDto $courtDto, Court $court, ?self $courtSchedule = null): self
    {
        if(is_null($courtSchedule)) {
            $courtSchedule = new self();
        }

        $courtSchedule->setDayOfWeek($courtDto->dayOfWeek);
        $courtSchedule->setStartTime($courtDto->startTime);
        $courtSchedule->setEndTime($courtDto->endTime);
        $courtSchedule->setCourt($court);

        return $courtSchedule;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfWeek(): int
    {
        return $this->dayOfWeek;
    }

    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    public function getEndTime(): \DateTime
    {
        return $this->endTime;
    }

    public function getCourt(): ?Court
    {
        return $this->court;
    }

    public function setDayOfWeek(int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    public function setStartTime(\DateTime $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function setEndTime(\DateTime $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function setCourt(?Court $court): self
    {
        $this->court = $court;
        return $this;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->getDayOfWeek() . ' - ' . $this->getStartTime()?->format('H:i:s');
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'dayOfWeek' => $this->dayOfWeek,
            'startTime' => $this->startTime?->format('H:i:s'),
            'endTime' => $this->endTime?->format('H:i:s'),
            'courtId' => $this->court?->getId(),
        ];
    }
}