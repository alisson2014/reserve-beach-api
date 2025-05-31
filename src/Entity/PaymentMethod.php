<?php

declare(strict_types=1);

namespace App\Entity;

use App\Interface\Arrayable;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: "payment_methods")]
#[ORM\HasLifecycleCallbacks]
class PaymentMethod implements Arrayable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    private string $name;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
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

    public function isActive(): bool
    {
        return $this->active;
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

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}