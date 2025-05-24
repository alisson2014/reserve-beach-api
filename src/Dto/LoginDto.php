<?php

declare(strict_types=1);

namespace App\Dto;

use App\Interface\Arrayable;
use Symfony\Component\Validator\Constraints as Assert;

final class LoginDto implements Dto, Arrayable
{
    #[Assert\NotBlank(message: "Email é obrigatório.")]
    #[Assert\Email(message: "O e-mail deve ser válido.")]
    #[Assert\Length(
        max: 254,
        maxMessage: "O e-mail deve ter no máximo {{ limit }} caracteres."
    )]
    public ?string $email = null;

    #[Assert\NotBlank(message: "Senha é obrigatória.")]
    public ?string $password = null;

    public function __construct(?string $email = null, ?string $password = null)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['email'] ?? null,
            $data['password'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}