<?php

namespace App\Controller;

use App\DTO\LoginDto;
use App\DTO\RegisterClientDto;
use App\Security\StatelessCsrfService;
use App\Service\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Contrôleur d'authentification (inscription, connexion, déconnexion).
 */
#[Route('/api/auth')]
class AuthController extends ApiController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly StatelessCsrfService $csrfService,
    ) {
    }

    /**
     * Fournit les tokens CSRF pour les formulaires sensibles.
     */
    #[Route('/csrf', name: 'api_auth_csrf', methods: ['GET'])]
    public function csrf(): JsonResponse
    {
        return $this->json([
            'login_token' => $this->csrfService->generate('authenticate'),
            'register_token' => $this->csrfService->generate('register'),
        ]);
    }

    /**
     * Inscription d'un nouveau client.
     */
    #[Route('/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        /** @var RegisterClientDto $dto */
        $dto = $this->serializer->deserialize($request->getContent(), RegisterClientDto::class, 'json');
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->validationErrorResponse($violations);
        }

        if (!$this->csrfService->isValid('register', $dto->csrfToken)) {
            return $this->json(['message' => 'Token CSRF invalide.'], Response::HTTP_FORBIDDEN);
        }

        try {
            $result = $this->authService->register($dto);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return $this->json($result, Response::HTTP_CREATED);
    }

    /**
     * Connexion client ou administrateur.
     */
    #[Route('/login', name: 'api_auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        /** @var LoginDto $dto */
        $dto = $this->serializer->deserialize($request->getContent(), LoginDto::class, 'json');
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            return $this->validationErrorResponse($violations);
        }

        if (!$this->csrfService->isValid('authenticate', $dto->csrfToken)) {
            return $this->json(['message' => 'Token CSRF invalide.'], Response::HTTP_FORBIDDEN);
        }

        try {
            $result = $this->authService->login($dto);
        } catch (AuthenticationException) {
            return $this->json(['message' => 'Identifiants invalides.'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($result);
    }

    /**
     * Déconnexion (invalidation côté client du token JWT).
     */
    #[Route('/logout', name: 'api_auth_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return $this->json(['message' => 'Déconnexion réussie.']);
    }
}
