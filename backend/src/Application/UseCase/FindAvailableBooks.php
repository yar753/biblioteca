<?php

namespace App\Application\UseCase;

use App\Application\Port\BookRepositoryInterface;

class FindAvailableBooks
{
    public function __construct(
        private BookRepositoryInterface $repository
    ) {
    }

    public function execute(): array
    {
        return $this->repository->findAvailable();
    }
}