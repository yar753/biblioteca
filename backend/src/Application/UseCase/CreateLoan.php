<?php

namespace App\Application\UseCase;

use App\Application\Port\BookRepositoryInterface;
use App\Application\Port\LoanRepositoryInterface;
use App\Application\Port\MemberRepositoryInterface;
use App\Application\Port\TransactionManagerInterface;
use App\Domain\Loan\Loan;

class CreateLoan
{
    public function __construct(
        private LoanRepositoryInterface $repository,
        private BookRepositoryInterface $bookRepository,
        private MemberRepositoryInterface $memberRepository,
        private TransactionManagerInterface $transactionManager
    ) {
    }

    public function execute(
    int $bookId,
    int $memberId,
    string $loanDate
): void {

        $loanDateObject = new \DateTimeImmutable($loanDate);

       $dueDate = $loanDateObject
            ->modify('+14 days')
            ->format('Y-m-d');

        $member = $this->memberRepository->findById($memberId);

        if ($member === null) {
            throw new \InvalidArgumentException(
                'El miembro no existe'
            );
        }

        if ((int) $member['is_active'] !== 1) {
            throw new \InvalidArgumentException(
                'El miembro está inactivo y no puede solicitar préstamos'
            );
        }

        $activeLoans = $this->repository->countActiveByMember($memberId);

        if ($activeLoans >= 3) {
            throw new \InvalidArgumentException(
                'El miembro no puede tener más de 3 préstamos activos'
            );
        }

        $book = $this->bookRepository->findById($bookId);

        if ($book === null) {
            throw new \InvalidArgumentException(
                'El libro no existe'
            );
        }

        if ((int) $book['available_copies'] <= 0) {
            throw new \InvalidArgumentException(
                'No hay copias disponibles de este libro'
            );
        }

        $loan = new Loan(
            null,
            $bookId,
            $memberId,
            $loanDate,
            $dueDate,
            null,
            'ACTIVE'
        );

        $this->transactionManager->begin();

        try {

            $this->repository->save($loan);

            $this->bookRepository->decreaseAvailableCopies($bookId);

            $this->transactionManager->commit();

        } catch (\Throwable $e) {

            $this->transactionManager->rollback();

            throw $e;
        }
    }
}