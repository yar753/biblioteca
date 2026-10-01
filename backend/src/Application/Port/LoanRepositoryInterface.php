<?php

namespace App\Application\Port;

use App\Domain\Loan\Loan;

interface LoanRepositoryInterface
{
    public function save(Loan $loan): void;

    public function findAll(): array;

    public function findFiltered(
       ?int $memberId,
        ?string $status
): array;

    public function findById(int $id): ?array;

    public function countActiveByMember(int $memberId): int;

    public function update(
        int $id,
        int $bookId,
        int $memberId,
        string $loanDate,
        string $dueDate,
        ?string $returnDate,
        string $status
    ): void;

    public function delete(int $id): void;
}
