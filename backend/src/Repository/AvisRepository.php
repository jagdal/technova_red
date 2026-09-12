<?php

namespace App\Repository;

use App\Entity\Avis;
use App\Entity\Client;
use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    /**
     * @return list<Avis>
     */
    public function findByProduitOrdered(Produit $produit): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.produit = :produit')
            ->setParameter('produit', $produit)
            ->orderBy('a.dateAvis', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByClientAndProduit(Client $client, Produit $produit): ?Avis
    {
        return $this->findOneBy(['client' => $client, 'produit' => $produit]);
    }

    /**
     * @return array{moyenne: float|null, nb_avis: int}
     */
    public function getStatsForProduit(Produit $produit): array
    {
        $result = $this->createQueryBuilder('a')
            ->select('AVG(a.note) as moyenne', 'COUNT(a.idAvis) as nb')
            ->andWhere('a.produit = :produit')
            ->setParameter('produit', $produit)
            ->getQuery()
            ->getSingleResult();

        $count = (int) ($result['nb'] ?? 0);

        return [
            'moyenne' => $count > 0 ? round((float) $result['moyenne'], 1) : null,
            'nb_avis' => $count,
        ];
    }
}
