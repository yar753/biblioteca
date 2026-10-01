<?php

namespace App\Infrastructure\Persistence;

use App\Application\Port\MemberRepositoryInterface;
use App\Domain\Member\Member;
use PDO;
use App\Application\Exception\DuplicateResourceException;

class PdoMemberRepository implements MemberRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function save(Member $member): void
{
    $sql = "
        INSERT INTO member (
            document_number,
            full_name,
            email,
            phone,
            is_active
        ) VALUES (
            :document_number,
            :full_name,
            :email,
            :phone,
            :is_active
        )
    ";

    $stmt = $this->pdo->prepare($sql);

    try {
        $stmt->execute([
            ':document_number' => $member->getDocumentNumber(),
            ':full_name' => $member->getFullName(),
            ':email' => $member->getEmail(),
            ':phone' => $member->getPhone(),
            ':is_active' => $member->isActive() ? 1 : 0,
        ]);
    } catch (\PDOException $e) {
    if ($e->errorInfo[1] === 1062) {

        if (str_contains($e->getMessage(), 'document_number')) {
            throw new DuplicateResourceException(
                'El número de documento ya existe'
            );
        }

        if (str_contains($e->getMessage(), 'email')) {
            throw new DuplicateResourceException(
                'El email ya existe'
            );
        }
    }

    throw $e;
}
}

    public function findAll(int $page = 1, int $size = 10): array
{
    $offset = ($page - 1) * $size;

    $sql = "
        SELECT
            id,
            document_number,
            full_name,
            email,
            phone,
            is_active,
            created_at,
            updated_at
        FROM member
        ORDER BY id DESC
        LIMIT :size OFFSET :offset
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':size', $size, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll();
}

public function countAll(): int
{
    $sql = "SELECT COUNT(*) FROM member";

    $stmt = $this->pdo->query($sql);

    return (int) $stmt->fetchColumn();
}

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                id,
                document_number,
                full_name,
                email,
                phone,
                is_active,
                created_at,
                updated_at
            FROM member
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $member = $stmt->fetch();

        return $member ?: null;
    }

    public function deactivate(int $id): void
{
    $sql = "
        UPDATE member
        SET is_active = 0
        WHERE id = :id
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);
}

    public function update(
        int $id,
        string $documentNumber,
        string $fullName,
        string $email,
        ?string $phone,
        bool $isActive
    ): void {
        $sql = "
            UPDATE member
            SET
                document_number = :document_number,
                full_name = :full_name,
                email = :email,
                phone = :phone,
                is_active = :is_active
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':document_number' => $documentNumber,
            ':full_name' => $fullName,
            ':email' => $email,
            ':phone' => $phone,
            ':is_active' => $isActive ? 1 : 0,
        ]);
    }

    public function delete(int $id): void
    {
        $sql = "
            DELETE FROM member
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);
    }
}