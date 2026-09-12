<?php

namespace App\Entity;

use App\Repository\FavoriRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Produit en favoris pour un client.
 */
#[ORM\Entity(repositoryClass: FavoriRepository::class)]
#[ORM\Table(name: 'favori')]
#[ORM\UniqueConstraint(name: 'uniq_favori_client_produit', columns: ['id_client', 'ref_produit'])]
class Favori
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_favori', type: 'integer')]
    private ?int $idFavori = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: 'id_client', referencedColumnName: 'id_client', nullable: false, onDelete: 'CASCADE')]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(name: 'ref_produit', referencedColumnName: 'ref_produit', nullable: false, onDelete: 'CASCADE')]
    private Produit $produit;

    #[ORM\Column(name: 'date_ajout', type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $dateAjout;

    public function getIdFavori(): ?int
    {
        return $this->idFavori;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;
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

    public function getDateAjout(): \DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): self
    {
        $this->dateAjout = $dateAjout;
        return $this;
    }
}
