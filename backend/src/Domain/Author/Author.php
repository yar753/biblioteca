<?php 
declare(strict_types=1);

namespace App\Domain\Author;

class Author
{
    public function __construct(
        private ?int $id,
        private string $firstName,
        private string $lastName,
        private ?string $nationality = null,
        private ?string $birthDate = null
    ) {
        if (trim($this->firstName) === '' ||
            trim($this->lastName) === '') {
            throw new \InvalidArgumentException(
                'El nombre y apellido son obligatorios.'
            );
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function getBirthDate(): ?string
    {
        return $this->birthDate;
    }
}