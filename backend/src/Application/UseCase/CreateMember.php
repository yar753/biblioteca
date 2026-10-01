<?php

namespace App\Application\UseCase;

use App\Application\Port\MemberRepositoryInterface;
use App\Domain\Member\Member;

class CreateMember
{
    public function __construct(
        private MemberRepositoryInterface $repository
    ) {
    }

    public function execute(
        string $documentNumber,
        string $fullName,
        string $email,
        ?string $phone
    ): void {
        $member = new Member(
            null,
            $documentNumber,
            $fullName,
            $email,
            $phone,
            true
        );

        $this->repository->save($member);
    }
}