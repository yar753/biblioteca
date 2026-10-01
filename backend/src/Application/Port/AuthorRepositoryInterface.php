<?php

namespace App\Application\Port;

use App\Domain\Author\Author;

interface AuthorRepositoryInterface
{
    public function save(Author $author): void;

    public function findAll(int $page = 1, int $size = 10): array;

    public function countAll(): int;

    public function findById(int $id): ?array;

    public function update(
        int $id,
        string $firstName,
        string $lastName,
        ?string $nationality,
        ?string $birthDate
    ): void;
    public function delete(int $id): void;
}