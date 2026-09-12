<?php

namespace App\Security;

use App\Entity\Admin;
use App\Entity\Client;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Génère et valide les tokens JWT pour l'authentification API.
 */
class TokenService
{
    private const TTL_SECONDS = 3600;

    public function __construct(
        private readonly string $jwtSecret,
    ) {
    }

    /**
     * Crée un token JWT pour un utilisateur authentifié.
     *
     * @return string Token JWT signé
     */
    public function createToken(UserInterface $user): string
    {
        $userType = $user instanceof Admin ? 'admin' : 'client';
        $now = time();

        $payload = [
            'sub' => $user->getUserIdentifier(),
            'user_type' => $userType,
            'roles' => $user->getRoles(),
            'iat' => $now,
            'exp' => $now + self::TTL_SECONDS,
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    /**
     * Décode et valide un token JWT.
     *
     * @return array{sub: string, user_type: string, roles: string[]}
     */
    public function decodeToken(string $token): array
    {
        $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));

        return [
            'sub' => $decoded->sub,
            'user_type' => $decoded->user_type,
            'roles' => $decoded->roles ?? [],
        ];
    }
}
