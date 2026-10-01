<?php

namespace App\Application\Port;

use App\Domain\Book\Book;

interface BookRepositoryInterface
{
    public function save(Book $book): void;

    public function findAll(int $page = 1, int $size = 10): array;

   public function countFiltered(
    ?string $title,
    ?int $authorId,
    ?bool $available
): int;

    public function findById(int $id): ?array;

    public function update(
        int $id,
        string $isbn,
        string $title,
        int $authorId,
        ?int $publicationYear,
        int $totalCopies,
        int $availableCopies
    ): void;

    public function delete(int $id): void;

    public function findFiltered(
    ?string $title,
    ?int $authorId,
    ?bool $available,
    int $page = 1,
    int $size = 10
): array;

    public function decreaseAvailableCopies(int $bookId): void;

    public function increaseAvailableCopies(int $bookId): void;
}