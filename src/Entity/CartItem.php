<?php

declare(strict_types=1);

namespace App\Entity;

use App\Interface\Arrayable;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Override;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "cart_items")]
class CartItem implements Arrayable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: "cart_id", referencedColumnName: "id", nullable: false)]
    private readonly Cart $cart;

    #[ORM\ManyToOne(targetEntity: CourtSchedule::class)]
    #[ORM\JoinColumn(name: "court_schedule_id", referencedColumnName: "id", nullable: false)]
    private readonly CourtSchedule $courtSchedule;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private readonly DateTimeImmutable $scheduleDate;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $addedAt;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active = true;

    public function __construct(Cart $cart, CourtSchedule $courtSchedule, DateTimeImmutable $scheduleDate, bool $active = true)
    {
        $this->cart = $cart;
        $this->courtSchedule = $courtSchedule;
        $this->scheduleDate = $scheduleDate;
        $this->active = $active;
        $this->addedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getCourtSchedule(): CourtSchedule
    {
        return $this->courtSchedule;
    }   

    public function getAddedAt(): DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function getScheduleDate(): DateTimeImmutable
    {
        return $this->scheduleDate;
    }

    public function isActive(): bool
    { 
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->courtSchedule->getStartTime()->format('Y-m-d H:i:s') . ' - ' . $this->courtSchedule->getDayOfWeek();        
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'cartId' => $this->cart->getId(),
            'courtSchedule' => $this->courtSchedule->toArray(),
            'scheduleDate' => $this->scheduleDate->format('c'),
            'addedAt' => $this->addedAt->format('c'),
            'active' => $this->active,
        ];
    }
}