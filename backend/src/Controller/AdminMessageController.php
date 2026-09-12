<?php

namespace App\Controller;

use App\Service\ContactService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion des messages de contact (admin).
 */
#[Route('/api/admin/messages')]
#[IsGranted('ROLE_ADMIN')]
class AdminMessageController extends ApiController
{
    public function __construct(
        private readonly ContactService $contactService,
    ) {
    }

    #[Route('', name: 'api_admin_messages_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json(['items' => $this->contactService->listForAdmin()]);
    }

    #[Route('/{id}/lu', name: 'api_admin_messages_mark_read', methods: ['PATCH'])]
    public function markRead(int $id): JsonResponse
    {
        $message = $this->contactService->markAsRead($id);
        if (!$message) {
            return $this->json(['message' => 'Message introuvable.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($message);
    }

    #[Route('/{id}', name: 'api_admin_messages_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        if (!$this->contactService->delete($id)) {
            return $this->json(['message' => 'Message introuvable.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['message' => 'Message supprimé.']);
    }
}
