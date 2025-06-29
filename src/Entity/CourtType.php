<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\CourtTypeDto;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Interface\Arrayable;
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Override;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: "court_types")]
#[ORM\HasLifecycleCallbacks]
class CourtType implements Arrayable
{
    public const int BEACH_TENNIS = 1;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;
    
    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    private string $name;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: "courtType", targetEntity: Court::class)]
    private Collection $courts;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->courts = new ArrayCollection();
    }

    public static function get(CourtTypeDto $courtTypeDto, ?self $courtType = null): self
    {
        if(is_null($courtType)) {
            $courtType = new self();
        }
        
        $courtType->setName($courtTypeDto->name);
        return $courtType;
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }   

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getCourts(): Collection
    {
        return $this->courts;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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
            'createdAt' => $this->createdAt->format("Y-m-d H:i:s"),
            'updatedAt' => $this->updatedAt?->format("Y-m-d H:i:s"),
        ];
    }
}