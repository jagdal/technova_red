<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un paiement lié à une commande.
 */
#[ORM\Entity(repositoryClass: PaiementRepository::class)]
#[ORM\Table(name: 'paiement')]
#[ORM\Index(name: 'idx_paiement_id_commande', columns: ['id_commande'])]
class Paiement
{
    /**
     * Référence technique du paiement.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ref_paiement', type: 'integer')]
    private ?int $refPaiement = null;

    /**
     * Mode de paiement.
     */
    #[ORM\Column(name: 'mode_paiement', type: 'string', length: 50)]
    private string $modePaiement;

    /**
     * Date du paiement.
     */
    #[ORM\Column(name: 'date_paiement', type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $datePaiement;

    /**
     * Montant payé.
     */
    #[ORM\Column(name: 'montant', type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $montant;

    /**
     * Référence externe Stripe (PaymentIntent ou Session ID).
     */
    #[ORM\Column(name: 'reference_transaction', type: 'string', length: 255, nullable: true)]
    private ?string $referenceTransaction = null;

    /**
     * Commande associée (1 paiement par commande).
     */
    #[ORM\OneToOne(inversedBy: 'paiement', targetEntity: Commande::class)]
    #[ORM\JoinColumn(name: 'id_commande', referencedColumnName: 'id_commande', nullable: false, onDelete: 'CASCADE', unique: true)]
    private Commande $commande;

    public function getRefPaiement(): ?int
    {
        return $this->refPaiement;
    }

    public function getModePaiement(): string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(string $modePaiement): self
    {
        $this->modePaiement = $modePaiement;
        return $this;
    }

    public function getDatePaiement(): \DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTimeInterface $datePaiement): self
    {
        $this->datePaiement = $datePaiement;
        return $this;
    }

    public function getMontant(): string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    public function getReferenceTransaction(): ?string
    {
        return $this->referenceTransaction;
    }

    public function setReferenceTransaction(?string $referenceTransaction): self
    {
        $this->referenceTransaction = $referenceTransaction;
        return $this;
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }

    public function setCommande(Commande $commande): self
    {
        $this->commande = $commande;
        return $this;
    }
}

