<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * Recherche paginée avec filtres catégorie, prix et texte.
     *
     * @return array{items: list<Produit>, total: int}
     */
    public function searchWithFilters(
        ?string $q,
        ?int $refCategorie,
        ?float $prixMin,
        ?float $prixMax,
        int $page,
        int $limit,
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.categorie', 'c')
            ->addSelect('c');

        if (null !== $q && '' !== trim($q)) {
            $qb->andWhere('p.libelleProduit LIKE :q OR p.description LIKE :q')
                ->setParameter('q', '%'.$q.'%');
        }

        if (null !== $refCategorie) {
            $qb->andWhere('c.refCategorie = :refCategorie')
                ->setParameter('refCategorie', $refCategorie);
        }

        if (null !== $prixMin) {
            $qb->andWhere('p.prix >= :prixMin')
                ->setParameter('prixMin', number_format($prixMin, 2, '.', ''));
        }

        if (null !== $prixMax) {
            $qb->andWhere('p.prix <= :prixMax')
                ->setParameter('prixMax', number_format($prixMax, 2, '.', ''));
        }

        $countQb = clone $qb;
        $total = (int) $countQb->select('COUNT(p.refProduit)')->getQuery()->getSingleScalarResult();

        $items = $qb
            ->orderBy('p.libelleProduit', 'ASC')
            ->setFirstResult(max(0, ($page - 1) * $limit))
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    public function findOneWithCategory(int $refProduit): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.categorie', 'c')
            ->addSelect('c')
            ->andWhere('p.refProduit = :id')
            ->setParameter('id', $refProduit)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return list<array{refProduit: int, libelleProduit: string}>
     */
    public function findIdAndLabels(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.refProduit', 'p.libelleProduit')
            ->getQuery()
            ->getArrayResult();
    }
}

