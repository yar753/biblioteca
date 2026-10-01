<?php

namespace App\Application\UseCase;

use App\Application\Port\MemberRepositoryInterface;

class DeleteMember
{
    public function __construct(
        private MemberRepositoryInterface $repository
    ) {
    }

    public function execute(int $id): void
    {
        $this->repository->delete($id);
    }
}