<?php

namespace App\Infrastructure\Persistence;

use App\Application\Port\BookRepositoryInterface;
use App\Domain\Book\Book;
use PDO;

class PdoBookRepository implements BookRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function save(Book $book): void
    {
        $sql = "
            INSERT INTO book (
                isbn,
                title,
                author_id,
                publication_year,
                total_copies,
                available_copies
            )
            VALUES (
                :isbn,
                :title,
                :author_id,
                :publication_year,
                :total_copies,
                :available_copies
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':isbn' => $book->getIsbn(),
            ':title' => $book->getTitle(),
            ':author_id' => $book->getAuthorId(),
            ':publication_year' => $book->getPublicationYear(),
            ':total_copies' => $book->getTotalCopies(),
            ':available_copies' => $book->getAvailableCopies(),
        ]);
    }

   public function findAll(int $page = 1, int $size = 10): array
{
    $offset = ($page - 1) * $size;

    $sql = "
        SELECT
            id,
            isbn,
            title,
            author_id,
            publication_year,
            total_copies,
            available_copies,
            created_at,
            updated_at
        FROM book
        ORDER BY id DESC
        LIMIT :size OFFSET :offset
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':size', $size, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll();
}

public function countFiltered(
    ?string $title,
    ?int $authorId,
    ?bool $available
): int {
    $sql = "
        SELECT COUNT(*)
        FROM book
        WHERE 1 = 1
    ";

    $params = [];

    if ($title !== null && trim($title) !== '') {
        $sql .= " AND title LIKE :title";
        $params[':title'] = '%' . $title . '%';
    }

    if ($authorId !== null) {
        $sql .= " AND author_id = :author_id";
        $params[':author_id'] = $authorId;
    }

    if ($available !== null) {
        if ($available) {
            $sql .= " AND available_copies > 0";
        } else {
            $sql .= " AND available_copies = 0";
        }
    }

    $stmt = $this->pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                id,
                isbn,
                title,
                author_id,
                publication_year,
                total_copies,
                available_copies,
                created_at,
                updated_at
            FROM book
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $book = $stmt->fetch();

        return $book ?: null;
    }

  public function findFiltered(
    ?string $title,
    ?int $authorId,
    ?bool $available,
    int $page = 1,
    int $size = 10
): array {
    $offset = ($page - 1) * $size;

    $sql = "
        SELECT
            id,
            isbn,
            title,
            author_id,
            publication_year,
            total_copies,
            available_copies,
            created_at,
            updated_at
        FROM book
        WHERE 1 = 1
    ";

    $params = [];

    if ($title !== null && trim($title) !== '') {
        $sql .= " AND title LIKE :title";
        $params[':title'] = '%' . $title . '%';
    }

    if ($authorId !== null) {
        $sql .= " AND author_id = :author_id";
        $params[':author_id'] = $authorId;
    }

    if ($available !== null) {
        if ($available) {
            $sql .= " AND available_copies > 0";
        } else {
            $sql .= " AND available_copies = 0";
        }
    }

    $sql .= "
        ORDER BY title ASC
        LIMIT :size OFFSET :offset
    ";

    $stmt = $this->pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->bindValue(':size', $size, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll();
}

    public function update(
    int $id,
    string $isbn,
    string $title,
    int $authorId,
    ?int $publicationYear,
    int $totalCopies,
    int $availableCopies
): void {
    $sql = "
        UPDATE book
        SET
            isbn = :isbn,
            title = :title,
            author_id = :author_id,
            publication_year = :publication_year,
            total_copies = :total_copies,
            available_copies = :available_copies
        WHERE id = :id
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ':id' => $id,
        ':isbn' => $isbn,
        ':title' => $title,
        ':author_id' => $authorId,
        ':publication_year' => $publicationYear,
        ':total_copies' => $totalCopies,
        ':available_copies' => $availableCopies,
    ]);

    }
    
    public function delete(int $id): void
{
    $sql = "
        DELETE FROM book
        WHERE id = :id
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);
}

public function decreaseAvailableCopies(int $bookId): void
{
    $sql = "
        UPDATE book
        SET available_copies = available_copies - 1
        WHERE id = :id
        AND available_copies > 0
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ':id' => $bookId
    ]);
}

public function increaseAvailableCopies(int $bookId): void
{
    $sql = "
        UPDATE book
        SET available_copies = available_copies + 1
        WHERE id = :id
        AND available_copies < total_copies
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ':id' => $bookId
    ]);
}

}