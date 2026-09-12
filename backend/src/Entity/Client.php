<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Représente un client (utilisateur final) de la plateforme.
 */
#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: 'client')]
#[ORM\UniqueConstraint(name: 'uniq_client_email', columns: ['email'])]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
   
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_client', type: 'integer')]
    private ?int $idClient = null;

    #[ORM\Column(name: 'nom_client', type: 'string', length: 100)]
    private string $nomClient;
   
    #[ORM\Column(name: 'prenom_client', type: 'string', length: 100)]
    private string $prenomClient;

    #[ORM\Column(name: 'email', type: 'string', length: 150, unique: true)]
    private string $email;

    #[ORM\Column(name: 'mot_de_passe', type: 'string', length: 255)]
    private string $motDePasse;

    #[ORM\Column(name: 'telephone', type: 'string', length: 20)]
    private string $telephone;

    #[ORM\Column(name: 'adresse', type: 'text')]
    private string $adresse;

    public function getIdClient(): ?int
    {
        return $this->idClient;
    }

    public function getNomClient(): string
    {
        return $this->nomClient;
    }

    public function setNomClient(string $nomClient): self
    {
        $this->nomClient = $nomClient;
        return $this;
    }

    public function getPrenomClient(): string
    {
        return $this->prenomClient;
    }

    public function setPrenomClient(string $prenomClient): self
    {
        $this->prenomClient = $prenomClient;
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

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return ['ROLE_CLIENT'];
    }

    public function getPassword(): string
    {
        return $this->motDePasse;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Rien à effacer : aucune donnée sensible temporaire n'est stockée ici.
    }
}

