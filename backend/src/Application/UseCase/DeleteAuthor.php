<?php

namespace App\Application\UseCase;

use App\Application\Port\AuthorRepositoryInterface;
use App\Application\Port\BookRepositoryInterface;

class DeleteAuthor
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository,
        private BookRepositoryInterface $bookRepository
    ) {
    }

    public function execute(int $id): void
    {
        $author = $this->authorRepository->findById($id);

        if ($author === null) {
            throw new \RuntimeException('Autor no encontrado');
        }

        $books = $this->bookRepository->findFiltered(
            null,
            $id,
            null,
            1,
            1
        );

        if (count($books) > 0) {
            throw new \InvalidArgumentException(
                'No se puede eliminar un autor que tiene libros asociados'
            );
        }

        $this->authorRepository->delete($id);
    }
}