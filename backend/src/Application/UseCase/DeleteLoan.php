<?php

namespace App\Application\UseCase;

use App\Application\Port\LoanRepositoryInterface;

class DeleteLoan
{
    public function __construct(
        private LoanRepositoryInterface $repository
    ) {
    }

    public function execute(int $id): void
    {
        $this->repository->delete($id);
    }
}