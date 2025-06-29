<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\CourtType;
use App\Interface\Arrayable;
use Symfony\Component\Validator\Constraints as Assert;

final class CourtDto implements Dto, Arrayable
{
    #[Assert\NotBlank(message: 'O nome da quadra é obrigatório.')]
    #[Assert\Length(max: 50, maxMessage: 'O nome da quadra não pode ter mais de {{ limit }} caracteres.')]
    public ?string $name = null;

    #[Assert\Length(max: 150, maxMessage: 'A descrição não pode ter mais de {{ limit }} caracteres.')]
    public ?string $description = null;

    #[Assert\NotBlank(message: 'A taxa de agendamento da quadra é obrigatória.')]
    #[Assert\Type(type: 'numeric', message: 'A taxa de agendamento deve ser numérica.')]
    #[Assert\GreaterThan(value: 0, message: 'A taxa de agendamento deve ser maior que 0.')]
    public ?float $schedulingFee = null;

    #[Assert\NotBlank(message: 'A capacidade da quadra é obrigatória.')]
    #[Assert\Type(type: 'integer', message: 'A capacidade deve ser um número inteiro.')]
    #[Assert\GreaterThanOrEqual(value: 1, message: 'A capacidade deve ser maior ou igual a 1.')]
    public ?int $capacity = null;

    #[Assert\Type(type: 'boolean', message: 'O campo ativo deve ser verdadeiro ou falso.')]
    public bool $active = true; 

    #[Assert\Length(max: 255, maxMessage: 'A URL da imagem não pode ter mais de {{ limit }} caracteres.')]
    #[Assert\Url(message: 'A URL da imagem deve ser uma URL válida.')]
    public ?string $imageUrl = null;

    #[Assert\Type(type: 'integer', message: 'O tipo de quadra deve ser um número inteiro.')]
    #[Assert\GreaterThan(value: 0, message: 'O tipo de quadra deve ser um número inteiro positivo.')]
    public ?int $courtTypeId = null;

    public function __construct(
        ?string $name = null,
        ?string $description = null,
        ?float $schedulingFee = null,
        ?int $capacity = null,
        bool $active = true,
        ?string $imageUrl = null,
        ?int $courtTypeId = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->schedulingFee = $schedulingFee;
        $this->capacity = $capacity;
        $this->active = $active;
        $this->imageUrl = $imageUrl;
        $this->courtTypeId = $courtTypeId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? null, 
            $data['description'] ?? null,
            isset($data['schedulingFee']) ? $data['schedulingFee'] : null,
            $data['capacity'] ?? null,
            $data['active'] ?? true,
            $data['imageUrl'] ?? null,
            isset($data['courtTypeId']) && is_int($data['courtTypeId']) ? $data['courtTypeId'] : CourtType::BEACH_TENNIS
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'schedulingFee' => $this->schedulingFee,
            'capacity' => $this->capacity,
            'active' => $this->active,
            'imageUrl' => $this->imageUrl,
            'courtTypeId' => $this->courtTypeId,
        ];
    }
}