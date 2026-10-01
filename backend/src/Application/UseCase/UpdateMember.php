<?php

namespace App\Application\UseCase;

use App\Application\Port\MemberRepositoryInterface;

class UpdateMember
{
    public function __construct(
        private MemberRepositoryInterface $repository
    ) {
    }

    public function execute(
        int $id,
        string $documentNumber,
        string $fullName,
        string $email,
        ?string $phone,
        bool $isActive
    ): void {
        $this->repository->update(
            $id,
            $documentNumber,
            $fullName,
            $email,
            $phone,
            $isActive
        );
    }
}