<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class LoginDto
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
}