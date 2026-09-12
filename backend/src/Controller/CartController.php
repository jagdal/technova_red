<?php

namespace App\Controller;

use App\DTO\CartItemDto;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Contrôleur de gestion du panier.
 */
#[Route('/api/panier')]
#[IsGranted('ROLE_CLIENT')]
class CartController extends ApiController
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    //Consulte le contenu du panier.
     
    #[Route('', name: 'api_cart_show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        return $this->json($this->cartService->getCartDetails());
    }
    // Ajoute ou met à jour un produit dans le panier.
    #[Route('/items', name: 'api_cart_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        return $this->handleItemUpdate($request);
    }
    // Modifie la quantité d'un produit dans le panier. 
    #[Route('/items/{refProduit}', name: 'api_cart_update', methods: ['PATCH'])]
    public function update(int $refProduit, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $data['refProduit'] = $refProduit;

        return $this->handleItemUpdate(new Request([], [], [], [], [], [], json_encode($data)));
    }
    // Supprime un produit du panier.
    #[Route('/items/{refProduit}', name: 'api_cart_remove', methods: ['DELETE'])]
    public function remove(int $refProduit): JsonResponse
    {
        try {
            $cart = $this->cartService->removeItem($refProduit);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($cart);
    }
//fin
    private function handleItemUpdate(Request $request): JsonResponse
    {
        /** @var CartItemDto $dto */
        $dto = $this->serializer->deserialize($request->getContent(), CartItemDto::class, 'json');
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->validationErrorResponse($violations);
        }

        try {
            $cart = $this->cartService->addOrUpdateItem($dto);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($cart);
    }
}
