<?php

namespace App\Repository;

use App\Entity\Subscription;
use App\Repository\Trait\RemoveTrait;
use App\Repository\Trait\SaveTrait;
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
    use SaveTrait;
    use RemoveTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }
}
