<?php

declare(strict_types=1);

namespace App\Dto;

use App\Interface\Arrayable;
use Symfony\Component\Validator\Constraints as Assert;

class CourtScheduleDto implements Dto, Arrayable
{
    #[Assert\NotBlank(message: 'O horário de início é obrigatório.')]
    #[Assert\DateTime(message: 'O horário de início deve ser uma data e hora válida.')]
    public ?\DateTimeInterface $startTime = null;

    #[Assert\NotBlank(message: 'O dia da semana é obrigatório.')]
    #[Assert\Type(type: 'integer', message: 'O dia da semana deve ser um número inteiro.')]
    #[Assert\Range(min: 1, max: 7, notInRangeMessage: 'O dia da semana deve ser entre 1 (Domingo) e 7 (Sábado).')]
    public ?int $dayOfWeek = null;

    #[Assert\NotBlank(message: 'O ID da quadra é obrigatório.')]
    #[Assert\Type(type: 'integer', message: 'O ID da quadra deve ser um número inteiro.')]
    public ?int $courtId = null;

    public function __construct(
        ?\DateTimeInterface $startTime = null,
        ?int $dayOfWeek = null,
        ?int $courtId = null
    ) {
        $this->startTime = $startTime;
        $this->dayOfWeek = $dayOfWeek;
        $this->courtId = $courtId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['startTime']) ? new \DateTime($data['startTime']) : null,
            $data['dayOfWeek'] ?? null,
            $data['courtId'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'startTime' => $this->startTime?->format('Y-m-d H:i:s'),
            'dayOfWeek' => $this->dayOfWeek,
            'courtId' => $this->courtId,
        ];
    }
}