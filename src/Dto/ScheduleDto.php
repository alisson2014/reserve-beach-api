<?php

declare(strict_types=1);

namespace App\Dto;

use App\Interface\Arrayable;
use Symfony\Component\Validator\Constraints as Assert;

final class ScheduleDto implements Dto, Arrayable
{
    #[Assert\NotBlank(message: 'O ID do usuário é obrigatório.')]
    #[Assert\Type(type: 'integer', message: 'O ID do usuário deve ser um número inteiro.')]
    public ?int $userId = null;

    #[Assert\NotBlank(message: 'O ID da quadra é obrigatório.')]
    #[Assert\Type(type: 'integer', message: 'O ID da quadra deve ser um número inteiro.')]
    public ?int $courtScheduleId = null;

    #[Assert\NotBlank(message: 'O ID do método de pagamento é obrigatório.')]
    #[Assert\Type(type: 'integer', message: 'O ID do método de pagamento deve ser um número inteiro.')]
    public ?int $paymentMethodId = null;

    #[Assert\NotBlank(message: 'A data e hora do agendamento são obrigatórias.')]
    public ?\DateTimeInterface $scheduledAt = null;

    #[Assert\NotBlank(message: 'A taxa de agendamento da quadra é obrigatória.')]
    #[Assert\Type(type: 'numeric', message: 'A taxa de agendamento deve ser numérica.')]
    #[Assert\GreaterThan(value: 0, message: 'A taxa de agendamento deve ser maior que 0.')]
    public ?float $totalValue = null;

    public function __construct(
        ?int $userId = null,
        ?int $courtScheduleId = null,
        ?int $paymentMethodId = null,
        ?\DateTimeInterface $scheduledAt = null,
        ?float $totalValue = null
    ) {
        $this->userId = $userId;
        $this->courtScheduleId = $courtScheduleId;
        $this->paymentMethodId = $paymentMethodId;
        $this->scheduledAt = $scheduledAt;
        $this->totalValue = $totalValue;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['userId'] ?? null,
            $data['courtScheduleId'] ?? null,
            $data['paymentMethodId'] ?? null,
            isset($data['scheduledAt']) ? new \DateTimeImmutable($data['scheduledAt']) : null,
            $data['totalValue'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'courtScheduleId' => $this->courtScheduleId,
            'paymentMethodId' => $this->paymentMethodId,
            'scheduledAt' => $this->scheduledAt?->format('Y-m-d H:i:s'),
            'totalValue' => $this->totalValue,
        ];
    }   
}