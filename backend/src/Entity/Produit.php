<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Représente un produit du catalogue.
 */
#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: 'produit')]
#[ORM\Index(name: 'idx_produit_ref_categorie', columns: ['ref_categorie'])]
#[ORM\Index(columns: ['prix'])]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('PRODUIT_VIEW', null)"),
        new Get(security: "is_granted('PRODUIT_VIEW', object)"),
        new Post(security: "is_granted('PRODUIT_CREATE', null)"),
        new Put(security: "is_granted('PRODUIT_EDIT', object)"),
        new Patch(security: "is_granted('PRODUIT_EDIT', object)"),
        new Delete(security: "is_granted('PRODUIT_DELETE', object)"),
    ],
    normalizationContext: ['groups' => ['produit:read']],
    denormalizationContext: ['groups' => ['produit:write']],
    paginationItemsPerPage: 12,
)]
#[ApiFilter(SearchFilter::class, properties: ['libelleProduit' => 'partial', 'categorie.refCategorie' => 'exact'])]
#[ApiFilter(RangeFilter::class, properties: ['prix'])]
class Produit
{
    // Référence technique du produit.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ref_produit', type: 'integer')]
    #[Groups(['produit:read'])]
    private ?int $refProduit = null;
    // Libellé du produit.
    #[ORM\Column(name: 'libelle_produit', type: 'string', length: 150)]
    #[Groups(['produit:read', 'produit:write'])]
    private string $libelleProduit;
    // Description du produit.
    #[ORM\Column(name: 'description', type: 'text')]
    #[Groups(['produit:read', 'produit:write'])]
    private string $description;
    // Prix du produit.  
    #[ORM\Column(name: 'prix', type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['produit:read', 'produit:write'])]
    private string $prix;
    // Stock disponible.
    #[ORM\Column(name: 'stock', type: 'integer')]
    #[Groups(['produit:read', 'produit:write'])]
    private int $stock;
    //URL de l'image du produit.
    #[ORM\Column(name: 'url_image', type: 'string', length: 500, nullable: true)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $urlImage = null;
    //Catégorie à laquelle appartient le produit.
    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(name: 'ref_categorie', referencedColumnName: 'ref_categorie', nullable: false, onDelete: 'RESTRICT')]
    #[Groups(['produit:read', 'produit:write'])]
    private Categorie $categorie;

    /**
     * @var Collection<int, Contenir>
     */
    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Contenir::class, orphanRemoval: false)]
    private Collection $contenus;

    public function __construct()
    {
        $this->contenus = new ArrayCollection();
    }

    public function getRefProduit(): ?int
    {
        return $this->refProduit;
    }

    public function getLibelleProduit(): string
    {
        return $this->libelleProduit;
    }

    public function setLibelleProduit(string $libelleProduit): self
    {
        $this->libelleProduit = $libelleProduit;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function getUrlImage(): ?string
    {
        return $this->urlImage;
    }

    public function setUrlImage(?string $urlImage): self
    {
        $this->urlImage = $urlImage;
        return $this;
    }

    public function getCategorie(): Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(Categorie $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    /**
     * @return Collection<int, Contenir>
     */
    public function getContenus(): Collection
    {
        return $this->contenus;
    }
}

