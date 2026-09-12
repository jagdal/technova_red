<?php

namespace App\Controller;

use App\DTO\OrderStatusUpdateDto;
use App\Entity\Client;
use App\Entity\Commande;
use App\Service\OrderService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Contrôleur de gestion des commandes.
 */
class CommandeController extends ApiController
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * Valide le panier et crée une commande.
     */
    #[Route('/api/commandes/checkout', name: 'api_commande_checkout', methods: ['POST'])]
    #[IsGranted('ROLE_CLIENT')]
    public function checkout(): JsonResponse
    {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return $this->json(['message' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
        }

        try {
            $commande = $this->orderService->checkout($client);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($commande, Response::HTTP_CREATED);
    }

    /**
     * Liste les commandes du client connecté.
     */
    #[Route('/api/commandes', name: 'api_commande_list', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function list(): JsonResponse
    {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return $this->json(['message' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
        }

        return $this->json($this->orderService->getClientOrders($client));
    }

    /**
     * Détail d'une commande.
     */
    #[Route('/api/commandes/{id}', name: 'api_commande_show', methods: ['GET'])]
    #[IsGranted('COMMANDE_VIEW', subject: 'commande')]
    public function show(#[MapEntity(mapping: ['id' => 'idCommande'])] Commande $commande): JsonResponse
    {
        return $this->json($this->orderService->serializeCommande($commande));
    }

    /**
     * Met à jour le statut d'une commande (admin).
     */
    #[Route('/api/admin/commandes/{id}/statut', name: 'api_commande_update_status', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateStatus(#[MapEntity(mapping: ['id' => 'idCommande'])] Commande $commande, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('COMMANDE_EDIT_STATUS', $commande);

        /** @var OrderStatusUpdateDto $dto */
        $dto = $this->serializer->deserialize($request->getContent(), OrderStatusUpdateDto::class, 'json');
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->validationErrorResponse($violations);
        }

        try {
            $result = $this->orderService->updateStatus($commande, $dto->statut);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($result);
    }

    /**
     * Liste toutes les commandes (admin).
     */
    #[Route('/api/admin/commandes', name: 'api_admin_commande_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminList(): JsonResponse
    {
        return $this->json($this->orderService->getAllOrdersForAdmin());
    }
}
