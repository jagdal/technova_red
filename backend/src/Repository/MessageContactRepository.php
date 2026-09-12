<?php

namespace App\Repository;

use App\Entity\MessageContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessageContact>
 */
class MessageContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageContact::class);
    }

    /**
     * @return MessageContact[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.dateEnvoi', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countUnread(): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.idMessage)')
            ->andWhere('m.lu = false')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
