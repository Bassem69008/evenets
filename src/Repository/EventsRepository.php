<?php

namespace App\Repository;

use App\Entity\Events;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Void_;

/**
 * @extends ServiceEntityRepository<Events>
 *
 * @method Events|null find($id, $lockMode = null, $lockVersion = null)
 * @method Events|null findOneBy(array $criteria, array $orderBy = null)
 * @method Events[]    findAll()
 * @method Events[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Events::class);
    }


   public  function save(events $events , bool $flush = true): Events

   {
       $this->_em->persist($events);
       if($flush)
       {
           $this->_em->flush();
       }
       return $events;
   }

   public function remove(events $events , bool $flush = true): Void
   {
       $this->_em->remove($events);
       if($flush)
       {
           $this->_em->flush();
       }
   }
}
