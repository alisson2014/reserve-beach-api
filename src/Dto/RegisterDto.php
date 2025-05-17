<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

#[AppAssert\PasswordMatch]
final class RegisterDto
{
    #[Assert\NotBlank(message: "Nome é obrigatório.")]
    #[Assert\Length(
        min: 5,
        max: 50,
        maxMessage: "O nome deve ter no máximo {{ limit }} caracteres.",
        minMessage: "O nome deve ter no mínimo {{ limit }} caracteres."
    )]
    public ?string $name = null;

    #[Assert\NotBlank(message: "Sobrenome é obrigatório.")]
    #[Assert\Length(
        min: 5,
        max: 150,
        maxMessage: "O sobrenome deve ter no máximo {{ limit }} caracteres.",
        minMessage: "O sobrenome deve ter no mínimo {{ limit }} caracteres."
    )]
    public ?string $lastName = null;

    #[Assert\NotBlank(message: "Email é obrigatório.")]
    #[Assert\Email(message: "O e-mail deve ser válido.")]
    #[Assert\Length(
        max: 254,
        maxMessage: "O e-mail deve ter no máximo {{ limit }} caracteres."
    )]
    public ?string $email = null;

    #[Assert\NotBlank(message: "Senha é obrigatória.")]
    #[Assert\Length(
        min: 8,
        minMessage: "A senha deve conter no mínimo {{ limit }} caracteres."
    )]
    #[Assert\Regex(
        pattern: "/^(?=.*[A-Z])(?=.*\d).+$/",
        message: "A senha deve conter pelo menos uma letra maiúscula e um número."
    )]
    public ?string $password = null;

    #[Assert\NotBlank(message: "Confirmação de senha é obrigatória.")]
    public ?string $confirmPassword = null;
}