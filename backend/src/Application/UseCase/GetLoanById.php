<?php

namespace App\Application\UseCase;

use App\Application\Port\LoanRepositoryInterface;

class GetLoanById
{
    public function __construct(
        private LoanRepositoryInterface $repository
    ) {
    }

    public function execute(int $id): ?array
    {
        return $this->repository->findById($id);
    }
}