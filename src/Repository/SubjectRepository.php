<?php

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subject>
 *
 * @method Subject|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subject|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subject[]    findAll()
 * @method Subject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    public function save(Subject $subject, bool $flush = true): Subject
    {
        $this->_em->persist($subject);
        if ($flush) {
            $this->_em->flush();
        }

        return $subject;
    }

    public function remove(Subject $subject, bool $flush = true): void
    {
        $this->_em->remove($subject);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
