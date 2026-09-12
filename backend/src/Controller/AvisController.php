<?php

namespace App\Controller;

use App\DTO\AvisDto;
use App\Entity\Client;
use App\Service\AvisService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Contrôleur des avis clients sur les produits.
 */
class AvisController extends ApiController
{
    public function __construct(
        private readonly AvisService $avisService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * Liste les avis d'un produit (public).
     */
    #[Route('/api/catalogue/produits/{id}/avis', name: 'api_avis_list', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function listByProduct(int $id): JsonResponse
    {
        try {
            $data = $this->avisService->getByProduct($id);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json($data);
    }

    /**
     * Publie un avis (client connecté, un seul avis par produit).
     */
    #[Route('/api/avis', name: 'api_avis_create', methods: ['POST'])]
    #[IsGranted('ROLE_CLIENT')]
    public function create(Request $request): JsonResponse
    {
        $client = $this->getUser();
        if (!$client instanceof Client) {
            return $this->json(['message' => 'Accès refusé.'], Response::HTTP_FORBIDDEN);
        }

        /** @var AvisDto $dto */
        $dto = $this->serializer->deserialize($request->getContent(), AvisDto::class, 'json');
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->validationErrorResponse($violations);
        }

        try {
            $avis = $this->avisService->create(
                $client,
                (int) $dto->refProduit,
                (int) $dto->note,
                $dto->commentaire,
            );
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($avis, Response::HTTP_CREATED);
    }
}
