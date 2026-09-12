<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Représente une catégorie de produits.
 */
#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ORM\Table(name: 'categorie')]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('CATEGORIE_VIEW', null)"),
        new Get(security: "is_granted('CATEGORIE_VIEW', object)"),
        new Post(security: "is_granted('CATEGORIE_CREATE', null)"),
        new Put(security: "is_granted('CATEGORIE_EDIT', object)"),
        new Patch(security: "is_granted('CATEGORIE_EDIT', object)"),
        new Delete(security: "is_granted('CATEGORIE_DELETE', object)"),
    ],
    normalizationContext: ['groups' => ['categorie:read']],
    denormalizationContext: ['groups' => ['categorie:write']],
)]
class Categorie
{
    // Référence technique de la catégorie.
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ref_categorie', type: 'integer')]
    #[Groups(['categorie:read', 'produit:read'])]
    private ?int $refCategorie = null;

     // Nom de la catégorie.

    #[ORM\Column(name: 'nom_categorie', type: 'string', length: 100)]
    #[Groups(['categorie:read', 'categorie:write', 'produit:read'])]
    private string $nomCategorie;

    // Description de la catégorie.
    #[ORM\Column(name: 'description', type: 'text')]
    #[Groups(['categorie:read', 'categorie:write'])]
    private string $description;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Produit::class, orphanRemoval: false)]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getRefCategorie(): ?int
    {
        return $this->refCategorie;
    }

    public function getNomCategorie(): string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(string $nomCategorie): self
    {
        $this->nomCategorie = $nomCategorie;
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

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }
}

