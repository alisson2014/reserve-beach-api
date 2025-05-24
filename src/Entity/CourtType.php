<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\CourtTypeDto;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Interface\Arrayable;
use Doctrine\Common\Collections\Collection;
use Override;

#[ORM\Entity]
#[ORM\Table(name: "court_types")]
#[ORM\HasLifecycleCallbacks]
class CourtType implements Arrayable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;
    
    #[ORM\Column(type: "string", length: 100, unique: true)]
    private string $name;

    #[ORM\Column(type: "datetime")]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $updatedAt = null;

    #[ORM\OneToMany(mappedBy: "courtType", targetEntity: Court::class)]
    private Collection $courts;

    public function __construct()
    {
        $this->createdAt = new DateTime();
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

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }   

    public function getUpdatedAt(): ?DateTime
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
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}