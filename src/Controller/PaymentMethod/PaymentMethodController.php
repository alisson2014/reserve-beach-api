<?php

declare(strict_types=1);

namespace App\Controller\PaymentMethod;

use App\Repository\PaymentMethodRepository\IPaymentMethodRepository;
use App\Utils\ResponseUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/payment_methods')]
class PaymentMethodController extends AbstractController
{
    use ResponseUtils;

    public const NOT_FOUND_MESSAGE = 'Método de pagamento não encontrado.';

    public function __construct(
        private IPaymentMethodRepository $paymentMethodRepository
    ) {}

    #[Route(name: 'payment_methods', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        $active = $request->query->get('active');

        $paymentMethods = array_map(
            fn ($paymentMethod) => $paymentMethod->toArray(),
            $this->paymentMethodRepository->getAll($name, $active)
        );

        return $this->ok($paymentMethods);
    }   

    #[Route('/enable', name: 'payment_methods_enable', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enable(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        return $this->setStatus($data['ids'] ?? []);
    }

    #[Route('/disable', name: 'payment_methods_disable', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function disable(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        return $this->setStatus($data['ids'] ?? [], false);
    }

    private function setStatus(array $ids, bool $active = true): JsonResponse
    {
        if (empty($ids)) {
            return $this->json(['error' => 'IDs não informados.'], Response::HTTP_BAD_REQUEST);
        }

        if ($active) {
            $this->paymentMethodRepository->enable($ids);
        } else {
            $this->paymentMethodRepository->disable($ids);
        }

        return $this->ok($ids, 'Métodos de pagamento atualizados com sucesso.');
    }
}