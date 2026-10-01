<?php

namespace App\Application\UseCase;

use App\Application\Port\BookRepositoryInterface;

class UpdateBook
{
    public function __construct(
        private BookRepositoryInterface $repository
    ) {
    }

    public function execute(
        int $id,
        string $isbn,
        string $title,
        int $authorId,
        ?int $publicationYear,
        int $totalCopies,
        int $availableCopies
    ): void {
        $this->repository->update(
            $id,
            $isbn,
            $title,
            $authorId,
            $publicationYear,
            $totalCopies,
            $availableCopies
        );
    }
}