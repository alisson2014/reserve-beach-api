<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CourtDto
{
    #[Assert\NotBlank(message: 'O nome da quadra é obrigatório.')]
    #[Assert\Length(max: 50, maxMessage: 'O nome da quadra não pode ter mais de {{ limit }} caracteres.')]
    public ?string $name = null;

    #[Assert\Length(max: 150, maxMessage: 'A descrição não pode ter mais de {{ limit }} caracteres.')]
    public ?string $description = null;

    #[Assert\NotBlank(message: 'A taxa de agendamento da quadra é obrigatória.')]
    #[Assert\Type(type: 'numeric', message: 'O valor deve ser numérico.')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'O valor não pode ser negativo.')]
    public ?float $schedulingFee = null;

    #[Assert\NotBlank(message: 'A capacidade da quadra é obrigatória.')]
    #[Assert\Type(type: 'integer', message: 'A capacidade deve ser um número inteiro.')]
    #[Assert\GreaterThanOrEqual(value: 1, message: 'A capacidade deve ser maior ou igual a 1.')]
    public ?int $capacity = null;

    #[Assert\Type(type: 'boolean', message: 'O campo ativo deve ser verdadeiro ou falso.')]
    public bool $active = true; 

    #[Assert\NotBlank(message: 'O tipo de quadra é obrigatório.')]
    #[Assert\Type(type: 'integer', message: 'O tipo de quadra deve ser um número inteiro.')]
    public ?int $courtTypeId = null;
}