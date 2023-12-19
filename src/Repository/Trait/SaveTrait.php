<?php

namespace App\Repository\Trait;

trait SaveTrait
{
    public function save(object $entity, bool $flush = true): object
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }

        return $entity;
    }
}
