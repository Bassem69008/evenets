<?php

namespace App\Service;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class PaginatorService
{
    public function __construct(private PaginatorInterface $paginator)
    {
    }

    public function paginate($query, Request $request)
    {
        return $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
    }
}
