<?php

namespace App\Application\UseCase;

use App\Application\Port\BookRepositoryInterface;
use App\Domain\Book\Book;

class CreateBook
{
    public function __construct(
        private BookRepositoryInterface $repository
    ) {
    }

    public function execute(
        string $isbn,
        string $title,
        int $authorId,
        ?int $publicationYear,
        int $totalCopies = 1
    ): void {
        $book = new Book(
            null,
            $isbn,
            $title,
            $authorId,
            $publicationYear,
            $totalCopies,
            $totalCopies
        );

        $this->repository->save($book);
    }
}