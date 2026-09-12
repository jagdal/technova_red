<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Représente un administrateur de la plateforme.
 */
#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\Table(name: 'admin')]
#[ORM\UniqueConstraint(name: 'uniq_admin_email', columns: ['email'])]
class Admin implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Matricule technique de l'administrateur.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'matricule_admin', type: 'integer')]
    private ?int $matriculeAdmin = null;

    /**
     * Nom de l'administrateur.
     */
    #[ORM\Column(name: 'nom_admin', type: 'string', length: 100)]
    private string $nomAdmin;

    /**
     * Email unique de l'administrateur (identifiant de connexion).
     */
    #[ORM\Column(name: 'email', type: 'string', length: 150, unique: true)]
    private string $email;

    /**
     * Mot de passe haché de l'administrateur.
     */
    #[ORM\Column(name: 'mot_de_passe', type: 'string', length: 255)]
    private string $motDePasse;

    public function getMatriculeAdmin(): ?int
    {
        return $this->matriculeAdmin;
    }

    public function getNomAdmin(): string
    {
        return $this->nomAdmin;
    }

    public function setNomAdmin(string $nomAdmin): self
    {
        $this->nomAdmin = $nomAdmin;
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

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
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

