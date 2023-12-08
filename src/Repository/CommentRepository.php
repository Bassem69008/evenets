<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }


    public function findAll()
    {
        return $this->findBy(array(), array('created_at' => 'DESC'));
    }
    public function save(Comment $comment, bool $flush = true): Comment
    {
        $this->_em->persist($comment);
        if ($flush) {
            $this->_em->flush();
        }

        return $comment;
    }

    public function remove(Comment $comment, bool $flush = true): void
    {
        $this->_em->remove($comment);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
