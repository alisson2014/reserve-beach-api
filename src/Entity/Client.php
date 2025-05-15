<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "client")]
#[ORM\HasLifecycleCallbacks]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[Orm\Column(type: "string", length: 50)]
    private string $name;

    #[Orm\Column(type: "string", length: 150)]
    private string $lastName;

    #[Orm\Column(type: "string", length: 254, unique: true)]
    private string $email;

    #[Orm\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(type: "string", length: 1, options: ["fixed" => true])]
    private string $status = 's';

    #[ORM\Column(type: "string", length: 11, nullable: true)]
    private ?string $phone;

    #[ORM\Column(type: "string", length: 11, nullable: true)]
    private ?string $cpf;

    #[ORM\Column(type: "datetime", nullable: true)]
    private DateTime $birthDate;

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

    /**
     * Verifica se a senha fornecida corresponde ao hash armazenado.
     *
     * @param string $plainPassword
     * @return bool
     */
    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }

    public function getPassword(): string 
    {
        return $this->password;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPhone(): ?string 
    {
        return $this->phone;
    }

    public function getCpf(): string 
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

    /**
     * @param string $email
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setEmail(string $email): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
            throw new \InvalidArgumentException("Email inválido.");
        }

        $this->email = $email;
        return $this;
    }

    public function setPassword(string $password): self 
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }   

    /**
     * Define o status do cliente.
     * Aceita apenas 's' (sim) ou 'n' (não).
     *
     * @param string $status
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setStatus(string $status): self
    {
        if (!in_array($status, ['s', 'n'])) {
            throw new \InvalidArgumentException("Status deve ser 's' ou 'n'.");
        }

        $this->status = $status;
        return $this;
    }

    public function setPhone(?string $phone): self 
    {
        if (!is_null($phone) && !preg_match('/^\d{11}$/', $phone)) {
            throw new \InvalidArgumentException("Telefone deve conter 11 dígitos.");
        }

        $this->phone = $phone;
        return $this;
    }

    public function setCpf(?string $cpf): self 
    {
        if (!is_null($cpf) && !preg_match('/^\d{11}$/', $cpf)) {
            throw new \InvalidArgumentException("CPF deve conter 11 dígitos.");
        }

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
        if (!is_null($birthDate) && $birthDate > new DateTime()) {
            throw new \InvalidArgumentException("Data de nascimento não pode ser futura.");
        }

        $this->birthDate = $birthDate;
        return $this;
    }
}
