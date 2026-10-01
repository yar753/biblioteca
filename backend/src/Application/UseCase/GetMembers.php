<?php

namespace App\Application\UseCase;

use App\Application\Port\MemberRepositoryInterface;

class GetMembers
{
    public function __construct(
        private MemberRepositoryInterface $repository
    ) {
    }

    public function execute(int $page = 1, int $size = 10): array
{
    return $this->repository->findAll($page, $size);
}
}