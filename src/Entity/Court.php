<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\CourtDto;
use App\Interface\Arrayable;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Override;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "courts")]
#[ORM\HasLifecycleCallbacks]
class Court implements Arrayable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50, unique: true)]
    private string $name;

    #[ORM\Column(type: "string", length: 150, nullable: true)]  
    private ?string $description = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $schedulingFee;

    #[ORM\Column(type: "smallint")]
    private int $capacity;

    #[ORM\Column(type: "boolean")]
    private bool $active = true;

    #[ORM\ManyToOne(targetEntity: CourtType::class, inversedBy: 'courts')]
    #[ORM\JoinColumn(name: "court_type_id", referencedColumnName: "id", nullable: false)]
    private CourtType $courtType;

    #[ORM\Column(type: "datetime")]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $updatedAt = null;

    /**
     * Coleção de todos os horários de funcionamento desta quadra.
     */
    #[ORM\OneToMany(targetEntity: CourtSchedule::class, mappedBy: 'court', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $schedules;

    public function __construct()
    {
        $this->createdAt = new DateTime();
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
        $this->updatedAt = new DateTime();
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

    public function getCourtType(): CourtType
    {
        return $this->courtType;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }   

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
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

    public function setCourtType(CourtType $courtType): self
    {
        $this->courtType = $courtType;
        return $this;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'schedulingFee' => $this->schedulingFee,
            'capacity' => $this->capacity,
            'active' => $this->active,
            'courtType' => $this->courtType->toArray(),
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}