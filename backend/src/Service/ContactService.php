<?php

namespace App\Service;

use App\DTO\ContactMessageDto;
use App\Entity\MessageContact;
use App\Repository\MessageContactRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Gestion des messages de contact.
 */
class ContactService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageContactRepository $messageContactRepository,
    ) {
    }

    public function submit(ContactMessageDto $dto): MessageContact
    {
        $message = (new MessageContact())
            ->setNomExpediteur($dto->nom)
            ->setEmail($dto->email)
            ->setSujet($dto->sujet)
            ->setContenu($dto->message)
            ->setDateEnvoi(new \DateTime())
            ->setLu(false);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listForAdmin(): array
    {
        return array_map(
            fn (MessageContact $m) => $this->serialize($m),
            $this->messageContactRepository->findAllOrdered()
        );
    }

    public function markAsRead(int $id): ?array
    {
        $message = $this->messageContactRepository->find($id);
        if (!$message) {
            return null;
        }

        $message->setLu(true);
        $this->entityManager->flush();

        return $this->serialize($message);
    }

    public function delete(int $id): bool
    {
        $message = $this->messageContactRepository->find($id);
        if (!$message) {
            return false;
        }

        $this->entityManager->remove($message);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function serialize(MessageContact $message): array
    {
        return [
            'id_message' => $message->getIdMessage(),
            'nom' => $message->getNomExpediteur(),
            'email' => $message->getEmail(),
            'sujet' => $message->getSujet(),
            'message' => $message->getContenu(),
            'date_envoi' => $message->getDateEnvoi()->format('Y-m-d H:i:s'),
            'lu' => $message->isLu(),
        ];
    }
}
