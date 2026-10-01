<?php

namespace App\Application\UseCase;

use App\Application\Port\BookRepositoryInterface;
use App\Application\Port\LoanRepositoryInterface;
use App\Application\Port\TransactionManagerInterface;

class ReturnLoan
{
   public function __construct(
    private LoanRepositoryInterface $loanRepository,
    private BookRepositoryInterface $bookRepository,
    private TransactionManagerInterface $transactionManager
) {
}

    public function execute(
        int $loanId,
        string $returnDate
    ): void {
        $loan = $this->loanRepository->findById($loanId);

        if ($loan === null) {
            throw new \InvalidArgumentException(
                'El préstamo no existe'
            );
        }

        if ($loan['status'] === 'RETURNED') {
            throw new \InvalidArgumentException(
                'El préstamo ya fue devuelto'
            );
        }

        $this->transactionManager->begin();

try {
    $this->loanRepository->update(
        $loanId,
        (int) $loan['book_id'],
        (int) $loan['member_id'],
        $loan['loan_date'],
        $loan['due_date'],
        $returnDate,
        'RETURNED'
    );

    $this->bookRepository->increaseAvailableCopies(
        (int) $loan['book_id']
    );

    $this->transactionManager->commit();
} catch (\Throwable $e) {
    $this->transactionManager->rollback();

    throw $e;
}
    }
}