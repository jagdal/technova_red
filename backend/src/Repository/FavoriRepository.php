<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Favori;
use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favori>
 */
class FavoriRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favori::class);
    }

    /**
     * @return list<Favori>
     */
    public function findByClientOrdered(Client $client): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.client = :client')
            ->setParameter('client', $client)
            ->orderBy('f.dateAjout', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByClientAndProduit(Client $client, Produit $produit): ?Favori
    {
        return $this->findOneBy(['client' => $client, 'produit' => $produit]);
    }

    /**
     * @return list<int>
     */
    public function findProductIdsByClient(Client $client): array
    {
        $rows = $this->createQueryBuilder('f')
            ->select('IDENTITY(f.produit) as ref_produit')
            ->andWhere('f.client = :client')
            ->setParameter('client', $client)
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): int => (int) $row['ref_produit'], $rows);
    }
}
