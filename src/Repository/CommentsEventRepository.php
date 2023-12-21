<?php

namespace App\Repository;

use App\Entity\CommentsEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentsEvent>
 *
 * @method CommentsEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentsEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentsEvent[]    findAll()
 * @method CommentsEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentsEvent::class);
    }

//    /**
//     * @return CommentsEvent[] Returns an array of CommentsEvent objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CommentsEvent
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
