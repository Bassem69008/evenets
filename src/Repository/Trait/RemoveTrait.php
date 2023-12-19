<?php

namespace App\Repository\Trait;

trait RemoveTrait
{
    public function remove(object $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
