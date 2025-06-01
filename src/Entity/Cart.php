<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\CartStatus;
use App\Interface\Arrayable;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Override;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "carts")]
#[ORM\HasLifecycleCallbacks]
class Cart implements Arrayable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'carts')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private readonly User $user;

    #[ORM\Column(enumType: CartStatus::class, length: 2, options: ["fixed" => true])] 
    private CartStatus $status = CartStatus::OPEN;

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

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }   

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function getStatus(): CartStatus
    {
        return $this->status;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setStatus(CartStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    #[Override]
    public function __toString(): string
    {
        return sprintf(
            'Cart ID: %d, User: %s, Status: %s',
            $this->getId(),
            (string)$this->getUser(),
            $this->getStatus()->value
        );
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'user' => $this->getUser()->toArray(),
            'status' => $this->getStatus(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
        ];
    }
}