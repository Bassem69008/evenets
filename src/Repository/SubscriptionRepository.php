<?php

namespace App\Repository;

use App\Entity\Subject;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 *
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function save(Subscription $subscription, bool $flush = true): Subscription
    {
        $this->_em->persist($subscription);
        if ($flush) {
            $this->_em->flush();
        }

        return $subscription;
    }

    public function remove(Subscription $subscription, bool $flush = true): void
    {
        $this->_em->remove($subscription);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
