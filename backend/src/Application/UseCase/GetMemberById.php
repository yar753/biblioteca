<?php

namespace App\Application\UseCase;

use App\Application\Port\MemberRepositoryInterface;

class GetMemberById
{
    public function __construct(
        private MemberRepositoryInterface $repository
    ) {
    }

    public function execute(int $id): ?array
    {
        return $this->repository->findById($id);
    }
}