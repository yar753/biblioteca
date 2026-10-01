<?php

namespace App\Application\UseCase;

use App\Application\Port\AuthorRepositoryInterface;
use App\Domain\Author\Author;

class CreateAuthor
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository
    ) {
    }

    public function execute(
        string $firstName,
        string $lastName,
        ?string $nationality,
        ?string $birthDate
    ): void {
        $author = new Author(
            null,
            $firstName,
            $lastName,
            $nationality,
            $birthDate
        );

        $this->authorRepository->save($author);
    }
}