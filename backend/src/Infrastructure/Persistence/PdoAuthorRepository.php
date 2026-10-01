<?php

namespace App\Infrastructure\Persistence;

use App\Application\Port\AuthorRepositoryInterface;
use App\Domain\Author\Author;
use PDO;

class PdoAuthorRepository implements AuthorRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {
    }

    public function save(Author $author): void
    {
        $sql = "
            INSERT INTO author
            (
                first_name,
                last_name,
                nationality,
                birth_date
            )
            VALUES
            (
                :first_name,
                :last_name,
                :nationality,
                :birth_date
            )
        ";

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            ':first_name' => $author->getFirstName(),
            ':last_name' => $author->getLastName(),
            ':nationality' => $author->getNationality(),
            ':birth_date' => $author->getBirthDate(),
        ]);
    }

public function findAll(int $page = 1, int $size = 10): array
{
    $offset = ($page - 1) * $size;

    $sql = "
        SELECT
            id,
            first_name,
            last_name,
            nationality,
            birth_date
        FROM author
        ORDER BY id DESC
        LIMIT :size OFFSET :offset
    ";

    $statement = $this->connection->prepare($sql);

    $statement->bindValue(':size', $size, PDO::PARAM_INT);
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

    $statement->execute();

    return $statement->fetchAll();
}

public function countAll(): int
{
    $sql = "SELECT COUNT(*) FROM author";

    $statement = $this->connection->query($sql);

    return (int) $statement->fetchColumn();
}

    public function findById(int $id): ?array
{
    $sql = "
        SELECT
            id,
            first_name,
            last_name,
            nationality,
            birth_date
        FROM author
        WHERE id = :id
    ";

    $statement = $this->connection->prepare($sql);

    $statement->execute([
        ':id' => $id
    ]);

    $author = $statement->fetch();

    return $author ?: null;
}
public function update(
    int $id,
    string $firstName,
    string $lastName,
    ?string $nationality,
    ?string $birthDate
): void {
    $sql = "
        UPDATE author
        SET
            first_name = :first_name,
            last_name = :last_name,
            nationality = :nationality,
            birth_date = :birth_date
        WHERE id = :id
    ";

    $statement = $this->connection->prepare($sql);

    $statement->execute([
        ':id' => $id,
        ':first_name' => $firstName,
        ':last_name' => $lastName,
        ':nationality' => $nationality,
        ':birth_date' => $birthDate,
    ]);
}

public function delete(int $id): void
{
    $sql = "
        DELETE FROM author
        WHERE id = :id
    ";

    $statement = $this->connection->prepare($sql);

    $statement->execute([
        ':id' => $id
    ]);
}

}