<?php

namespace App\Entity;

use App\Repository\MessageContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message reçu via le formulaire de contact.
 */
#[ORM\Entity(repositoryClass: MessageContactRepository::class)]
#[ORM\Table(name: 'message_contact')]
class MessageContact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_message', type: 'integer')]
    private ?int $idMessage = null;

    #[ORM\Column(name: 'nom_expediteur', type: 'string', length: 100)]
    private string $nomExpediteur;

    #[ORM\Column(name: 'email', type: 'string', length: 150)]
    private string $email;

    #[ORM\Column(name: 'sujet', type: 'string', length: 200)]
    private string $sujet;

    #[ORM\Column(name: 'contenu', type: Types::TEXT)]
    private string $contenu;

    #[ORM\Column(name: 'date_envoi', type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $dateEnvoi;

    #[ORM\Column(name: 'lu', type: 'boolean')]
    private bool $lu = false;

    public function getIdMessage(): ?int
    {
        return $this->idMessage;
    }

    public function getNomExpediteur(): string
    {
        return $this->nomExpediteur;
    }

    public function setNomExpediteur(string $nomExpediteur): self
    {
        $this->nomExpediteur = $nomExpediteur;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSujet(): string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateEnvoi(): \DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function isLu(): bool
    {
        return $this->lu;
    }

    public function setLu(bool $lu): self
    {
        $this->lu = $lu;

        return $this;
    }
}
