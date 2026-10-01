<?php

namespace App\Application\Port;

use App\Domain\Member\Member;

interface MemberRepositoryInterface
{
    public function save(Member $member): void;

    public function findAll(int $page = 1, int $size = 10): array;

    public function countAll(): int;

    public function findById(int $id): ?array;

    public function deactivate(int $id): void;

    public function update(
        int $id,
        string $documentNumber,
        string $fullName,
        string $email,
        ?string $phone,
        bool $isActive
    ): void;

    public function delete(int $id): void;
}