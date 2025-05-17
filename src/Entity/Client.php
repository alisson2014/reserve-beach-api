<?php

namespace App\Entity;

use App\Enum\ClientStatus;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: "client")]
#[ORM\HasLifecycleCallbacks]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\Column(type: "string", length: 1, options: ["fixed" => true])]
    private ClientStatus $status = ClientStatus::ACTIVE;

    #[ORM\Column(type: "string", length: 11, nullable: true)]
    private ?string $phone;

    #[ORM\Column(type: "string", length: 11, nullable: true)]
    private ?string $cpf;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $birthDate;

    #[ORM\Column(type: "datetime")]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $updatedAt;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new DateTime();
        $this->createdAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
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

    public function getStatus(): ClientStatus
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

    public function setId(int $id): self 
    {
        $this->id = $id;
        return $this;
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

    public function setStatus(ClientStatus $status): self
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

    public function setUpdatedAt(DateTime $updatedAt): self 
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function setBirthDate(?DateTime $birthDate): self 
    {
        $this->birthDate = $birthDate;
        return $this;
    }
}
