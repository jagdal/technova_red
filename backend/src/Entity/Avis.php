<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Avis client sur un produit.
 */
#[ORM\Entity(repositoryClass: AvisRepository::class)]
#[ORM\Table(name: 'avis')]
#[ORM\UniqueConstraint(name: 'uniq_avis_client_produit', columns: ['id_client', 'ref_produit'])]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_avis', type: 'integer')]
    private ?int $idAvis = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: 'id_client', referencedColumnName: 'id_client', nullable: false, onDelete: 'CASCADE')]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(name: 'ref_produit', referencedColumnName: 'ref_produit', nullable: false, onDelete: 'CASCADE')]
    private Produit $produit;

    #[ORM\Column(name: 'note', type: 'smallint')]
    private int $note;

    #[ORM\Column(name: 'commentaire', type: Types::TEXT)]
    private string $commentaire;

    #[ORM\Column(name: 'date_avis', type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $dateAvis;

    public function getIdAvis(): ?int
    {
        return $this->idAvis;
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

    public function getNote(): int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getCommentaire(): string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getDateAvis(): \DateTimeInterface
    {
        return $this->dateAvis;
    }

    public function setDateAvis(\DateTimeInterface $dateAvis): self
    {
        $this->dateAvis = $dateAvis;
        return $this;
    }
}
