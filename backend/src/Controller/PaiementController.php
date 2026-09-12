<?php

namespace App\Controller;

use App\DTO\PaymentDto;
use App\Entity\Client;
use App\Service\PaymentService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Contrôleur de gestion des paiements.
 */
#[Route('/api/paiements')]
#[IsGranted('ROLE_CLIENT')]
class PaiementController extends ApiController
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * Effectue le paiement d'une commande.
     */
    #[Route('', name: 'api_paiement_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return $this->json(['message' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
        }

        /** @var PaymentDto $dto */
        $dto = $this->serializer->deserialize($request->getContent(), PaymentDto::class, 'json');
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->validationErrorResponse($violations);
        }

        try {
            $result = $this->paymentService->processPayment($dto, $client);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($result, Response::HTTP_CREATED);
    }
}
