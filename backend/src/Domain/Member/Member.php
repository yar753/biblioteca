<?php

namespace App\Domain\Member;

class Member
{
    public function __construct(
        private ?int $id,
        private string $documentNumber,
        private string $fullName,
        private string $email,
        private ?string $phone,
        private bool $isActive = true
    ) {
        if (trim($this->documentNumber) === '') {
            throw new \InvalidArgumentException(
                'El número de documento es obligatorio'
            );
        }

        if (trim($this->fullName) === '') {
            throw new \InvalidArgumentException(
                'El nombre completo es obligatorio'
            );
        }

        if (trim($this->email) === '') {
            throw new \InvalidArgumentException(
                'El email es obligatorio'
            );

        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
    throw new \InvalidArgumentException(
        'El email no tiene un formato válido'
    );
}
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocumentNumber(): string
    {
        return $this->documentNumber;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}