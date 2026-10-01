<?php

namespace App\Application\UseCase;

use App\Application\Port\AuthorRepositoryInterface;

class UpdateAuthor
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository
    ) {
    }

    public function execute(
        int $id,
        string $firstName,
        string $lastName,
        ?string $nationality,
        ?string $birthDate
    ): void {
        $author = $this->authorRepository->findById($id);

        if ($author === null) {
            throw new \RuntimeException('Autor no encontrado');
        }

        $this->authorRepository->update(
            $id,
            $firstName,
            $lastName,
            $nationality,
            $birthDate
        );
    }
}