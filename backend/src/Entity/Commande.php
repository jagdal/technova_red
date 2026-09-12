<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une commande passée par un client.
 */
#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commande')]
#[ORM\Index(name: 'idx_commande_id_client', columns: ['id_client'])]
class Commande
{
    /**
     * Identifiant technique de la commande.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_commande', type: 'integer')]
    private ?int $idCommande = null;

    /**
     * Date de la commande.
     */
    #[ORM\Column(name: 'date_commande', type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $dateCommande;

    /**
     * Total de la commande.
     */
    #[ORM\Column(name: 'total', type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $total;

    /**
     * Statut de la commande.
     */
    #[ORM\Column(name: 'statut', type: 'string', length: 50)]
    private string $statut;

    /**
     * Client ayant passé la commande.
     */
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: 'id_client', referencedColumnName: 'id_client', nullable: false, onDelete: 'RESTRICT')]
    private Client $client;

    /**
     * @var Collection<int, Contenir>
     */
    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: Contenir::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $contenus;

    /**
     * Paiement associé à la commande (1 paiement par commande).
     */
    #[ORM\OneToOne(mappedBy: 'commande', targetEntity: Paiement::class, cascade: ['persist', 'remove'])]
    private ?Paiement $paiement = null;

    public function __construct()
    {
        $this->contenus = new ArrayCollection();
    }

    public function getIdCommande(): ?int
    {
        return $this->idCommande;
    }

    public function getDateCommande(): \DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): self
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getTotal(): string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
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

    /**
     * @return Collection<int, Contenir>
     */
    public function getContenus(): Collection
    {
        return $this->contenus;
    }

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }

    public function setPaiement(?Paiement $paiement): self
    {
        $this->paiement = $paiement;
        return $this;
    }
}

