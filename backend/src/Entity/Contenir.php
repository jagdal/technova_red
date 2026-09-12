<?php

namespace App\Entity;

use App\Repository\ContenirRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité de liaison Commande ↔ Produit avec informations de quantité et prix unitaire.
 */
#[ORM\Entity(repositoryClass: ContenirRepository::class)]
#[ORM\Table(name: 'contenir')]
class Contenir
{
    /**
     * Commande liée (partie de la PK composite).
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'contenus')]
    #[ORM\JoinColumn(name: 'id_commande', referencedColumnName: 'id_commande', nullable: false, onDelete: 'CASCADE')]
    private Commande $commande;

    /**
     * Produit lié (partie de la PK composite).
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'contenus')]
    #[ORM\JoinColumn(name: 'ref_produit', referencedColumnName: 'ref_produit', nullable: false, onDelete: 'RESTRICT')]
    private Produit $produit;

    /**
     * Quantité commandée.
     */
    #[ORM\Column(name: 'quantite', type: 'integer')]
    private int $quantite;

    /**
     * Prix unitaire au moment de la commande.
     */
    #[ORM\Column(name: 'prix_unitaire', type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $prixUnitaire;

    public function getCommande(): Commande
    {
        return $this->commande;
    }

    public function setCommande(Commande $commande): self
    {
        $this->commande = $commande;
        return $this;
    }

    public function getProduit(): Produit
    {
        return $this->produit;
    }

    public function setProduit(Produit $produit): self
    {
        $this->produit = $produit;
        return $this;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getPrixUnitaire(): string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): self
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }
}

