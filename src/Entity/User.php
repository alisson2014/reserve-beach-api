<?php

namespace App\Entity;

use App\Dto\RegisterDto;
use App\Enum\UserStatus;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: "users")]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50)]
    private string $name;

    #[ORM\Column(type: "string", length: 150)]
    private string $lastName;

    #[ORM\Column(type: "string", length: 254, unique: true)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(enumType: UserStatus::class, length: 1, options: ["fixed" => true])] 
    private UserStatus $status = UserStatus::ACTIVE;

    #[ORM\Column(type: "string", length: 11, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: "string", length: 11, nullable: true)]
    private ?string $cpf = null;

    #[ORM\Column(type: "date_immutable", nullable: true)]
    private ?DateTimeImmutable $birthDate = null;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: "json")]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $position = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->roles = ['ROLE_USER'];
    }

    public static function getFromRegisterDto(RegisterDto $registerDto): self
    {
        $user = new self();
        $user->setName($registerDto->name);
        $user->setLastName($registerDto->lastName);
        $user->setEmail($registerDto->email);
        return $user;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
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

    public function getCreatedAt(): DateTimeImmutable 
    {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): ?DateTimeImmutable 
    {
        return $this->updatedAt;
    }

    public function getBirthDate(): ?DateTimeImmutable 
    {
        return $this->birthDate;
    }

    public function getPosition(): ?string
    {
        return $this->position;
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

    public function setBirthDate(?DateTimeImmutable $birthDate): self 
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;
        return $this;
    }

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
            'birthDate' => $this->getBirthDate() ? $this->getBirthDate()->format('Y-m-d') : null,
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $this->getUpdatedAt() ? $this->getUpdatedAt()->format('Y-m-d H:i:s') : null,
            'roles' => $this->getRoles(),
            'position' => $this->getPosition(),
        ];
    }
}
