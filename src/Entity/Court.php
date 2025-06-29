<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\CourtDto;
use App\Interface\Arrayable;
use DateTimeImmutable;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\DBAL\Types\Types;
use Override;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "courts")]
#[ORM\HasLifecycleCallbacks]
class Court implements Arrayable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 150, nullable: true)]  
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $schedulingFee;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $capacity;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active = true;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\ManyToOne(targetEntity: CourtType::class, inversedBy: 'courts')]
    #[ORM\JoinColumn(name: "court_type_id", referencedColumnName: "id", nullable: false)]
    private CourtType $courtType;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    /**
     * Coleção de todos os horários de funcionamento desta quadra.
     */
    #[ORM\OneToMany(targetEntity: CourtSchedule::class, mappedBy: 'court', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $schedules;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->schedules = new ArrayCollection();
    }

    /**
     * @return Collection<int, CourtSchedule>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(CourtSchedule $schedule): static
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules->add($schedule);
            $schedule->setCourt($this);
        }

        return $this;
    }

    public function removeSchedule(CourtSchedule $schedule): static
    {
        if ($this->schedules->removeElement($schedule)) {
            if ($schedule->getCourt() === $this) {
                $schedule->setCourt(null);
            }
        }

        return $this;
    }

    public static function get(CourtDto $courtDto, CourtType $courtType, ?self $court = null): self
    {
        if(is_null($court)) {
            $court = new self();
        }

        $court->name = $courtDto->name;
        $court->description = $courtDto->description;
        $court->schedulingFee = $courtDto->schedulingFee;
        $court->capacity = $courtDto->capacity;
        $court->active = $courtDto->active;
        $court->courtType = $courtType;

        return $court;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSchedulingFee(): float
    {
        return $this->schedulingFee;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getCourtType(): CourtType
    {
        return $this->courtType;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }   

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return !is_null($this->deletedAt);
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setSchedulingFee(float $schedulingFee): self
    {
        $this->schedulingFee = $schedulingFee;
        return $this;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function setCourtType(CourtType $courtType): self
    {
        $this->courtType = $courtType;
        return $this;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    #[Override]
    public function __toString(): string
    {
        $courtType = (string)$this->courtType;
        return $this->name . "({$courtType})";
    }

    #[Override]
    public function toArray(): array
    {
        $schedules = array_map(
            fn(CourtSchedule $schedule): array => $schedule->toArray(),
            $this->schedules->toArray()
        );

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'schedulingFee' => $this->schedulingFee,
            'capacity' => $this->capacity,
            'active' => $this->active,
            'imageUrl' => $this->imageUrl,
            'courtType' => $this->courtType->toArray(),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deletedAt' => $this->deletedAt?->format('Y-m-d H:i:s'),
            'schedules' => $schedules
        ];
    }
}