<?php

namespace App\Service;

use App\Repository\AdminRepository;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Gestion des utilisateurs côté administrateur.
 */
class AdminUserService
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly AdminRepository $adminRepository,
        private readonly CommandeRepository $commandeRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function listAll(): array
    {
        $clients = array_map(
            fn ($c) => [
                'id' => $c->getIdClient(),
                'type' => 'client',
                'nom' => $c->getNomClient(),
                'prenom' => $c->getPrenomClient(),
                'email' => $c->getEmail(),
                'telephone' => $c->getTelephone(),
                'adresse' => $c->getAdresse(),
            ],
            $this->clientRepository->findBy([], ['nomClient' => 'ASC'])
        );

        $admins = array_map(
            fn ($a) => [
                'id' => $a->getMatriculeAdmin(),
                'type' => 'admin',
                'nom' => $a->getNomAdmin(),
                'prenom' => null,
                'email' => $a->getEmail(),
                'telephone' => null,
                'adresse' => null,
            ],
            $this->adminRepository->findBy([], ['nomAdmin' => 'ASC'])
        );

        return [
            'clients' => $clients,
            'admins' => $admins,
            'total' => count($clients) + count($admins),
        ];
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function deleteClient(int $id): void
    {
        $client = $this->clientRepository->find($id);
        if (!$client) {
            throw new \InvalidArgumentException('Client introuvable.');
        }

        if ($this->commandeRepository->count(['client' => $client]) > 0) {
            throw new \InvalidArgumentException('Impossible de supprimer un client ayant des commandes.');
        }

        $this->entityManager->remove($client);
        $this->entityManager->flush();
    }
}
