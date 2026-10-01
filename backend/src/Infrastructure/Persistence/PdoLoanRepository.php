<?php

namespace App\Infrastructure\Persistence;

use App\Application\Port\LoanRepositoryInterface;
use App\Domain\Loan\Loan;
use PDO;

class PdoLoanRepository implements LoanRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function save(Loan $loan): void
    {
        $sql = "
            INSERT INTO loan (
                book_id,
                member_id,
                loan_date,
                due_date,
                return_date,
                status
            ) VALUES (
                :book_id,
                :member_id,
                :loan_date,
                :due_date,
                :return_date,
                :status
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':book_id' => $loan->getBookId(),
            ':member_id' => $loan->getMemberId(),
            ':loan_date' => $loan->getLoanDate(),
            ':due_date' => $loan->getDueDate(),
            ':return_date' => $loan->getReturnDate(),
            ':status' => $loan->getStatus(),
        ]);
    }

    public function findAll(): array
    {
        $sql = "
            SELECT
                id,
                book_id,
                member_id,
                loan_date,
                due_date,
                return_date,
                status
            FROM loan
            ORDER BY id DESC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function findFiltered(
    ?int $memberId,
    ?string $status
): array {
    $sql = "
        SELECT
            id,
            book_id,
            member_id,
            loan_date,
            due_date,
            return_date,
            status
        FROM loan
        WHERE 1 = 1
    ";

    $params = [];

    if ($memberId !== null) {
        $sql .= " AND member_id = :member_id";
        $params[':member_id'] = $memberId;
    }

    if ($status !== null && trim($status) !== '') {
        $sql .= " AND status = :status";
        $params[':status'] = $status;
    }

    $sql .= " ORDER BY loan_date DESC";

    $stmt = $this->pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();

    return $stmt->fetchAll();
}

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                id,
                book_id,
                member_id,
                loan_date,
                due_date,
                return_date,
                status
            FROM loan
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $loan = $stmt->fetch();

        return $loan ?: null;
    }

    public function countActiveByMember(int $memberId): int
{
    $sql = "
        SELECT COUNT(*)
        FROM loan
        WHERE member_id = :member_id
          AND status = 'ACTIVE'
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ':member_id' => $memberId
    ]);

    return (int) $stmt->fetchColumn();
}

    public function update(
        int $id,
        int $bookId,
        int $memberId,
        string $loanDate,
        string $dueDate,
        ?string $returnDate,
        string $status
    ): void {
        $sql = "
            UPDATE loan
            SET
                book_id = :book_id,
                member_id = :member_id,
                loan_date = :loan_date,
                due_date = :due_date,
                return_date = :return_date,
                status = :status
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':book_id' => $bookId,
            ':member_id' => $memberId,
            ':loan_date' => $loanDate,
            ':due_date' => $dueDate,
            ':return_date' => $returnDate,
            ':status' => $status,
        ]);
    }

    public function delete(int $id): void
    {
        $sql = "
            DELETE FROM loan
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);
    }
}