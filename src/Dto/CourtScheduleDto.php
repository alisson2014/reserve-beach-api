<?php

declare(strict_types=1);

namespace App\Dto;

use App\Interface\Arrayable;
use Symfony\Component\Validator\Constraints as Assert;

class CourtScheduleDto implements Dto, Arrayable
{
    #[Assert\NotBlank(message: 'O horário de início é obrigatório.')]
    public ?\DateTimeInterface $startTime = null;

    #[Assert\NotBlank(message: 'O horário de término é obrigatório.')]
    public ?\DateTimeInterface $endTime = null;

    #[Assert\NotBlank(message: 'O dia da semana é obrigatório.')]
    #[Assert\Type(type: 'integer', message: 'O dia da semana deve ser um número inteiro.')]
    #[Assert\Range(min: 0, max: 6, notInRangeMessage: 'O dia da semana deve ser entre 0 (Domingo) e 6 (Sábado).')]
    public ?int $dayOfWeek = null;

    #[Assert\NotBlank(message: 'O ID da quadra é obrigatório.')]
    #[Assert\Type(type: 'integer', message: 'O ID da quadra deve ser um número inteiro.')]
    public ?int $courtId = null;

    public function __construct(
        ?\DateTimeInterface $startTime = null,
        ?\DateTimeInterface $endTime = null,
        ?int $dayOfWeek = null,
        ?int $courtId = null
    ) {
        $this->startTime = $startTime;
        $this->endTime = $endTime;  
        $this->dayOfWeek = $dayOfWeek;
        $this->courtId = $courtId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['startTime']) ? new \DateTime($data['startTime']) : null,
            isset($data['endTime']) ? new \DateTime($data['endTime']) : null,
            $data['dayOfWeek'] ?? null,
            $data['courtId'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'startTime' => $this->startTime?->format('Y-m-d H:i:s'),
            'endTime' => $this->endTime?->format('Y-m-d H:i:s'),
            'dayOfWeek' => $this->dayOfWeek,
            'courtId' => $this->courtId,
        ];
    }
}