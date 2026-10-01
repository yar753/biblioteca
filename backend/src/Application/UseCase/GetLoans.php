<?php

namespace App\Application\UseCase;

use App\Application\Port\LoanRepositoryInterface;

class GetLoans
{
    public function __construct(
        private LoanRepositoryInterface $repository
    ) {
    }

    public function execute(
    ?int $memberId = null,
    ?string $status = null
): array {
    return $this->repository->findFiltered($memberId, $status);
}

}