<?php

namespace App\Application\UseCase;

use App\Application\Port\LoanRepositoryInterface;

class UpdateLoan
{
    public function __construct(
        private LoanRepositoryInterface $repository
    ) {
    }

    public function execute(
        int $id,
        int $bookId,
        int $memberId,
        string $loanDate,
        string $dueDate,
        ?string $returnDate,
        string $status
    ): void {
        $this->repository->update(
            $id,
            $bookId,
            $memberId,
            $loanDate,
            $dueDate,
            $returnDate,
            $status
        );
    }
}