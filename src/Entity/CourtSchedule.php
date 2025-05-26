<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\CourtScheduleDto;
use App\Interface\Arrayable;
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
    private ?int $dayOfWeek = null;

    /**
     * A hora de início do slot de agendamento.
     * Ex: 08:00:00, 19:00:00
     */
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $startTime = null;

    /**
     * A quadra à qual este horário pertence.
     */
    #[ORM\ManyToOne(targetEntity: Court::class, inversedBy: 'schedules')]
    #[ORM\JoinColumn(name: 'court_id', referencedColumnName: 'id', nullable: false)]
    private ?Court $court = null;

    public static function get(CourtScheduleDto $courtDto, Court $court, ?self $courtSchedule = null): self
    {
        if(is_null($courtSchedule)) {
            $courtSchedule = new self();
        }

        $courtSchedule->setDayOfWeek($courtDto->dayOfWeek);
        $courtSchedule->setStartTime($courtDto->startTime);
        $courtSchedule->setCourt($court);

        return $courtSchedule;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
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
            'id' => $this->getId(),
            'day_of_week' => $this->getDayOfWeek(),
            'start_time' => $this->getStartTime()?->format('H:i:s'),
            'court' => $this->getCourt(),
        ];
    }
}