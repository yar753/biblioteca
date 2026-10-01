<?php

namespace App\Application\UseCase;

use App\Application\Port\BookRepositoryInterface;
use App\Application\Port\LoanRepositoryInterface;

class DeleteBook
{
    public function __construct(
        private BookRepositoryInterface $repository,
        private LoanRepositoryInterface $loanRepository
    ) {
    }

    public function execute(int $id): void
    {
        $activeLoans = $this->loanRepository->findFiltered(
            null,
            'ACTIVE'
        );

        foreach ($activeLoans as $loan) {
            if ((int) $loan['book_id'] === $id) {
                throw new \InvalidArgumentException(
                    'No se puede eliminar un libro con préstamos activos'
                );
            }
        }

        $this->repository->delete($id);
    }
}