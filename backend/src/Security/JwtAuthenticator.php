<?php

namespace App\Security;

use App\Repository\AdminRepository;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Authenticator JWT pour les routes API protégées.
 */
class JwtAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly ClientRepository $clientRepository,
        private readonly AdminRepository $adminRepository,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        $authorization = $request->headers->get('Authorization');

        return is_string($authorization) && str_starts_with($authorization, 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $authorization = (string) $request->headers->get('Authorization');
        $rawToken = substr($authorization, 7);

        try {
            $payload = $this->tokenService->decodeToken($rawToken);
        } catch (\Throwable) {
            throw new CustomUserMessageAuthenticationException('Token invalide ou expiré.');
        }

        $email = $payload['sub'];
        $userType = $payload['user_type'];

        return new SelfValidatingPassport(
            new UserBadge($email, function () use ($email, $userType) {
                $user = 'admin' === $userType
                    ? $this->adminRepository->findOneBy(['email' => $email])
                    : $this->clientRepository->findOneBy(['email' => $email]);

                if (null === $user) {
                    throw new CustomUserMessageAuthenticationException('Utilisateur introuvable.');
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Token invalide/expiré : on laisse passer en anonyme (routes publiques).
        // Les routes protégées seront bloquées par access_control.
        return null;
    }
}
