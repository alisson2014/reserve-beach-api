<?php

declare(strict_types=1);

namespace App\Entity;

use App\Interface\Arrayable;
use DateTimeImmutable;
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

    #[ORM\Column(type: "string", length: 150)]
    private string $description;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $schedulingFee;

    #[ORM\Column(type: "smallint")]
    private int $capacity;

    #[ORM\Column(type: "string", length: 1, options: ["fixed" => true])]
    private string $status;

    #[ORM\ManyToOne(targetEntity: CourtType::class)]
    #[ORM\JoinColumn(name: "court_type_id", referencedColumnName: "id", nullable: false)]
    private CourtType $courtType;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
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

    public function getDescription(): string
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

    public function getStatus(): string
    {
        return $this->status;
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

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(string $description): self
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

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setCourtType(CourtType $courtType): self
    {
        $this->courtType = $courtType;
        return $this;
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
            'status' => $this->status,
            'courtType' => $this->courtType->toArray(),
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}