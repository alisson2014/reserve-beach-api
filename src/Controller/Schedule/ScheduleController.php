<?php

declare(strict_types=1);

namespace App\Controller\Schedule;

use App\Dto\ScheduleDto;
use App\Entity\Schedule;
use App\Repository\CartItemRepository\ICartItemRepository;
use App\Repository\CourtScheduleRepository\ICourtScheduleRepository;
use App\Repository\PaymentMethodRepository\IPaymentMethodRepository;
use App\Repository\ScheduleRepository\IScheduleRepository;
use App\Repository\UserRepository\IUserRepository;
use App\Utils\ResponseUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/schedules')]
class ScheduleController extends AbstractController
{
    use ResponseUtils;

    public const NOT_FOUND_MESSAGE = 'Agendamento não encontrado.';

    public function __construct(
        private IScheduleRepository $scheduleRepository,
        private ICartItemRepository $cartItemRepository,
        private IUserRepository $userRepository,
        private ICourtScheduleRepository $courtScheduleRepository,
        private IPaymentMethodRepository $paymentMethodRepository
    ) {}

    #[Route(name: 'schedules', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = $request->toArray();
        $user = $this->getUser();
        if (!$user) {
            return $this->unauthorized('Usuário não autenticado.');
        }

        $user = $this->userRepository->getByEmail($user->getUserIdentifier());

        $schedules = [];
        foreach ($data as $item) {
            $item['userId'] = $user->getId();

            $scheduleDto = ScheduleDto::fromArray($item);
            $validationErrors = $validator->validate($scheduleDto);
            if (count($validationErrors) > 0) {
                return $this->badRequest($validationErrors);
            }

            $schedule = new Schedule($user, $this->courtScheduleRepository->getById($scheduleDto->courtScheduleId));
            $schedule->setScheduledAt($scheduleDto->scheduledAt);
            $schedule->setTotalValue($scheduleDto->totalValue);
            $schedule->setPaymentMethod($this->paymentMethodRepository->getById($scheduleDto->paymentMethodId));

            $schedules[] = $this->scheduleRepository->add($schedule, true);
            $this->cartItemRepository->disable($this->cartItemRepository->get($item['cartItemId']), true);
        }

        return $this->ok($schedules, "Agendamentos criados com sucesso.");
    }
}