<?php

namespace App\Application\UseCase;

use App\Application\Port\MemberRepositoryInterface;

class DeactivateMember
{
    public function __construct(
        private MemberRepositoryInterface $repository
    ) {
    }

    public function execute(int $memberId): void
    {
        $member = $this->repository->findById($memberId);

        if ($member === null) {
            throw new \InvalidArgumentException(
                'El miembro no existe'
            );
        }

        if ((int) $member['is_active'] !== 1) {
            throw new \InvalidArgumentException(
                'El miembro ya está inactivo'
            );
        }

        $this->repository->deactivate($memberId);
    }
}