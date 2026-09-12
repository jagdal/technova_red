<?php

namespace App\Controller;

use App\DTO\StripeCheckoutDto;
use App\Entity\Client;
use App\Repository\CommandeRepository;
use App\Service\StripePaymentService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Contrôleur Stripe Checkout — paiement sécurisé des commandes.
 */
#[Route('/api/paiements/stripe')]
class StripePaymentController extends ApiController
{
    public function __construct(
        private readonly StripePaymentService $stripePaymentService,
        private readonly CommandeRepository $commandeRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * Crée une session Stripe Checkout pour une commande en attente.
     */
    #[Route('/session', name: 'api_stripe_session_create', methods: ['POST'])]
    #[IsGranted('ROLE_CLIENT')]
    public function createSession(Request $request): JsonResponse
    {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return $this->json(['message' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
        }

        /** @var StripeCheckoutDto $dto */
        $dto = $this->serializer->deserialize($request->getContent(), StripeCheckoutDto::class, 'json');
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->validationErrorResponse($violations);
        }

        $commande = $this->commandeRepository->find($dto->idCommande);
        if (null === $commande) {
            return $this->json(['message' => 'Commande introuvable.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $result = $this->stripePaymentService->createCheckoutSession($commande, $client);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\RuntimeException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $this->json($result);
    }

    /**
     * Confirme le paiement après redirection Stripe (page succès).
     */
    #[Route('/confirm', name: 'api_stripe_confirm', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function confirm(Request $request): JsonResponse
    {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return $this->json(['message' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
        }

        $sessionId = trim((string) $request->query->get('session_id', ''));
        if ('' === $sessionId) {
            return $this->json(['message' => 'Session Stripe manquante.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->stripePaymentService->confirmCheckoutSession($sessionId, $client);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\RuntimeException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $this->json($result);
    }

    /**
     * Webhook Stripe (checkout.session.completed).
     */
    #[Route('/webhook', name: 'api_stripe_webhook', methods: ['POST'])]
    public function webhook(Request $request): JsonResponse
    {
        try {
            $result = $this->stripePaymentService->handleWebhook(
                $request->getContent(),
                $request->headers->get('Stripe-Signature')
            );
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        if (null === $result) {
            return $this->json(['received' => true]);
        }

        return $this->json(['received' => true, 'payment' => $result]);
    }
}
