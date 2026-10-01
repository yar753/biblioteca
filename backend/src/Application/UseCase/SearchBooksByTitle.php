<?php

namespace App\Application\UseCase;

use App\Application\Port\BookRepositoryInterface;

class SearchBooksByTitle
{
    public function __construct(
        private BookRepositoryInterface $repository
    ) {
    }

    public function execute(string $title): array
    {
        return $this->repository->searchByTitle($title);
    }
}