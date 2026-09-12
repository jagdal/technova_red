<?php

namespace App\Security;

/**
 * Génère et valide des tokens CSRF stateless (sans session) pour l'API SPA.
 */
class StatelessCsrfService
{
    private const TTL_SECONDS = 3600;

    public function __construct(
        private readonly string $appSecret,
    ) {
    }

    /**
     * Génère un token CSRF pour une intention donnée (authenticate, register).
     */
    public function generate(string $intent): string
    {
        $expiresAt = time() + self::TTL_SECONDS;
        $payload = $intent.'|'.$expiresAt;
        $signature = hash_hmac('sha256', $payload, $this->appSecret);

        return rtrim(strtr(base64_encode($payload.'|'.$signature), '+/', '-_'), '=');
    }

    /**
     * Vérifie qu'un token CSRF est valide pour l'intention attendue.
     */
    public function isValid(string $intent, string $token): bool
    {
        $decoded = base64_decode(strtr($token, '-_', '+/'), true);
        if (false === $decoded) {
            return false;
        }

        $parts = explode('|', $decoded);
        if (3 !== count($parts)) {
            return false;
        }

        [$tokenIntent, $expiresAt, $signature] = $parts;
        if ($tokenIntent !== $intent) {
            return false;
        }

        if ((int) $expiresAt < time()) {
            return false;
        }

        $payload = $tokenIntent.'|'.$expiresAt;
        $expected = hash_hmac('sha256', $payload, $this->appSecret);

        return hash_equals($expected, $signature);
    }
}
