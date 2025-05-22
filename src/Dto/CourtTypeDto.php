<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CourtTypeDto
{
    #[Assert\NotBlank(message: "Nome é obrigatório.")]
    #[Assert\Length(
        max: 100,
        min: 2,
        maxMessage: "O nome deve ter no máximo {{ limit }} caracteres.",
        minMessage: "O nome deve ter no mínimo {{ limit }} caracteres."
    )]
    public ?string $name = null;
}