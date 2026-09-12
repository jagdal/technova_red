<?php

namespace App\Service;

use App\DTO\LoginDto;
use App\DTO\RegisterClientDto;
use App\Entity\Admin;
use App\Entity\Client;
use App\Repository\AdminRepository;
use App\Repository\ClientRepository;
use App\Security\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Service d'authentification et d'inscription des utilisateurs.
 */
class AuthService
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly AdminRepository $adminRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenService $tokenService,
    ) {
    }

    /**
     * Inscrit un nouveau client.
     *
     * @return array{token: string, user: array<string, mixed>}
     */
    public function register(RegisterClientDto $dto): array
    {
        if (null !== $this->clientRepository->findOneBy(['email' => $dto->email])) {
            throw new \InvalidArgumentException('Un compte existe déjà avec cet email.');
        }

        $client = (new Client())
            ->setNomClient($dto->nomClient)
            ->setPrenomClient($dto->prenomClient)
            ->setEmail($dto->email)
            ->setTelephone($dto->telephone)
            ->setAdresse($dto->adresse);

        $client->setMotDePasse(
            $this->passwordHasher->hashPassword($client, $dto->motDePasse)
        );

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return [
            'token' => $this->tokenService->createToken($client),
            'user' => $this->serializeClient($client),
        ];
    }

    /**
     * Authentifie un client ou un administrateur.
     *
     * @return array{token: string, user: array<string, mixed>, roles: string[]}
     */
    public function login(LoginDto $dto): array
    {
        $user = $this->clientRepository->findOneBy(['email' => $dto->email])
            ?? $this->adminRepository->findOneBy(['email' => $dto->email]);

        if (null === $user || !$this->passwordHasher->isPasswordValid($user, $dto->motDePasse)) {
            throw new AuthenticationException('Identifiants invalides.');
        }

        $userData = $user instanceof Admin
            ? $this->serializeAdmin($user)
            : $this->serializeClient($user);

        return [
            'token' => $this->tokenService->createToken($user),
            'user' => $userData,
            'roles' => $user->getRoles(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeClient(Client $client): array
    {
        return [
            'id' => $client->getIdClient(),
            'nom' => $client->getNomClient(),
            'prenom' => $client->getPrenomClient(),
            'email' => $client->getEmail(),
            'telephone' => $client->getTelephone(),
            'adresse' => $client->getAdresse(),
            'type' => 'client',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeAdmin(Admin $admin): array
    {
        return [
            'id' => $admin->getMatriculeAdmin(),
            'nom' => $admin->getNomAdmin(),
            'email' => $admin->getEmail(),
            'type' => 'admin',
        ];
    }
}
