<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\ScheduleDto;
use App\Interface\Arrayable;
use App\Repository\ScheduleRepository\ScheduleRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Override;

#[ORM\Entity(ScheduleRepository::class)]
#[ORM\Table(name: "schedules")]
#[ORM\HasLifecycleCallbacks]
class Schedule implements Arrayable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $scheduledAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'schedules')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private readonly User $user;

    #[ORM\ManyToOne(targetEntity: CourtSchedule::class, inversedBy: 'schedules')]
    #[ORM\JoinColumn(name: "court_schedule_id", referencedColumnName: "id", nullable: false)]
    private readonly CourtSchedule $courtSchedule;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private readonly float $totalValue;

    #[ORM\ManyToOne(targetEntity: PaymentMethod::class)]
    #[ORM\JoinColumn(name: "payment_method_id", referencedColumnName: "id", nullable: false)]
    private readonly PaymentMethod $paymentMethod;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $transactionId = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public static function get(
        ScheduleDto $scheduleDto,
        ?self $schedule = null,
        EntityManagerInterface $em
    ): self
    {
        if (is_null($schedule)) {
            $schedule = new self();
        }

        $schedule->user = $em->find(User::class, $scheduleDto->userId);
        $schedule->courtSchedule = $em->find(CourtSchedule::class, $scheduleDto->courtScheduleId);
        $schedule->scheduledAt = $scheduleDto->scheduledAt;
        $schedule->totalValue = $scheduleDto->totalValue;
        $schedule->paymentMethod = $em->find(PaymentMethod::class, $scheduleDto->paymentMethodId);

        return $schedule;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCourtSchedule(): CourtSchedule
    {
        return $this->courtSchedule;
    }

    public function getScheduledAt(): DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function getTotalValue(): float
    {
        return $this->totalValue;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(?string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    public function setScheduledAt(DateTimeImmutable $scheduledAt): void
    {
        $this->scheduledAt = $scheduledAt;
    }

    public function setTotalValue(float $totalValue): void
    {
        $this->totalValue = $totalValue;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    #[Override]
    public function __toString(): string
    {
        return sprintf(
            'Schedule ID: %d, User: %s, Court Schedule: %s, Total Value: %.2f, Payment Method: %s, Scheduled At: %s',
            $this->id,
            (string)$this->user,
            (string)$this->courtSchedule,
            $this->totalValue,
            (string)$this->paymentMethod,
            $this->scheduledAt->format('Y-m-d H:i:s')
        );
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->toArray(),
            'courtSchedule' => $this->courtSchedule->toArray(),
            'totalValue' => $this->totalValue,
            'paymentMethod' => $this->paymentMethod->toArray(),
            'transactionId' => $this->transactionId,
            'scheduledAt' => $this->scheduledAt->format('Y-m-d H:i:s'),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}