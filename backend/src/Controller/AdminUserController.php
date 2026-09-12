<?php

namespace App\Controller;

use App\Service\AdminUserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion des utilisateurs (admin).
 */
#[Route('/api/admin/utilisateurs')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends ApiController
{
    public function __construct(
        private readonly AdminUserService $adminUserService,
    ) {
    }

    #[Route('', name: 'api_admin_users_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json($this->adminUserService->listAll());
    }

    #[Route('/clients/{id}', name: 'api_admin_users_delete_client', methods: ['DELETE'])]
    public function deleteClient(int $id): JsonResponse
    {
        try {
            $this->adminUserService->deleteClient($id);
        } catch (\InvalidArgumentException $e) {
            $status = str_contains($e->getMessage(), 'introuvable')
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_CONFLICT;

            return $this->json(['message' => $e->getMessage()], $status);
        }

        return $this->json(['message' => 'Client supprimé.']);
    }
}
