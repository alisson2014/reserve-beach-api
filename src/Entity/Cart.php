<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\CartStatus;
use App\Interface\Arrayable;
use App\Repository\CartRepository\CartRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Override;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\{ArrayCollection, Collection};

#[ORM\Entity(CartRepository::class)]
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

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $expiresAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'cart', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->items = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->expiresAt = (new DateTimeImmutable())->add(new DateInterval('PT15M'));
    }   

    /**
     * @return Collection<int, CartItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
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

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }   

    public function getUpdatedAt(): ?DateTimeImmutable
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
            'items' => array_map(fn(CartItem $item): array => $item->toArray(), $this->getItems()->toArray()),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'expiresAt' => $this->expiresAt->format('Y-m-d H:i:s'),
        ];
    }
}