<?php

declare(strict_types=1);

namespace App\Dto;

use App\Interface\Arrayable;
use Symfony\Component\Validator\Constraints as Assert;

final class CourtTypeDto implements Dto, Arrayable
{
    #[Assert\NotBlank(message: "Nome é obrigatório.")]
    #[Assert\Length(
        max: 100,
        min: 2,
        maxMessage: "O nome deve ter no máximo {{ limit }} caracteres.",
        minMessage: "O nome deve ter no mínimo {{ limit }} caracteres."
    )]
    public ?string $name = null;

    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}