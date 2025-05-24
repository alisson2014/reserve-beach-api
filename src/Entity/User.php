<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\RegisterDto;
use App\Enum\UserStatus;
use App\Interface\Arrayable;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Component\Security\Core\User\{UserInterface, PasswordAuthenticatedUserInterface};
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: "users")]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface, Arrayable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 150)]
    private string $lastName;

    #[ORM\Column(type: Types::STRING, length: 254, unique: true)]
    private string $email;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $password;

    #[ORM\Column(enumType: UserStatus::class, length: 1, options: ["fixed" => true])] 
    private UserStatus $status = UserStatus::ACTIVE;

    #[ORM\Column(type: Types::STRING, length: 11, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::STRING, length: 11, nullable: true, unique: true)]
    private ?string $cpf = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $birthDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $updatedAt = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = ['ROLE_USER'];

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public static function get(RegisterDto $registerDto, ?self $user = null): self
    {
        if(is_null($user)) {
            $user = new self();
        }
        
        $user->setName($registerDto->name);
        $user->setLastName($registerDto->lastName);
        $user->setEmail($registerDto->email);
        return $user;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string 
    {
        return $this->name;
    }

    public function getLastName(): string 
    {
        return $this->lastName;
    }

    public function getEmail(): string 
    {
        return $this->email;
    }

    public function getPassword(): string 
    {
        return $this->password;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function getPhone(): ?string 
    {
        return $this->phone;
    }

    public function getCpf(): ?string 
    {
        return $this->cpf;
    }

    public function getCreatedAt(): DateTime 
    {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): ?DateTime 
    {
        return $this->updatedAt;
    }

    public function getBirthDate(): ?DateTime 
    {
        return $this->birthDate;
    }

    public function setName(string $name): self 
    {
        $this->name = $name;
        return $this;
    }

    public function setLastName(string $lastName): self 
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPassword(string $password): self 
    {
        $this->password = $password;
        return $this;
    }   

    public function setStatus(UserStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setPhone(?string $phone): self 
    {
        $this->phone = $phone;
        return $this;
    }

    public function setCpf(?string $cpf): self 
    {
        $this->cpf = $cpf;
        return $this;
    }

    public function setBirthDate(?DateTime $birthDate): self 
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name . ' ' . $this->lastName;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'lastName' => $this->getLastName(),
            'email' => $this->getEmail(),
            'status' => $this->getStatus()->value,
            'phone' => $this->getPhone(),
            'cpf' => $this->getCpf(),
            'birthDate' => $this->getBirthDate() ?: null,
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt() ?: null,
            'roles' => $this->getRoles()
        ];
    }
}
