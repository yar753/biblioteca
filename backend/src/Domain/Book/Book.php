<?php

namespace App\Domain\Book;

class Book
{
    public function __construct(
        private ?int $id,
        private string $isbn,
        private string $title,
        private int $authorId,
        private ?int $publicationYear,
        private int $totalCopies = 1,
        private int $availableCopies = 1
    ) {
        if (trim($this->isbn) === '') {
            throw new \InvalidArgumentException(
                'El ISBN es obligatorio'
            );
        }

        if (!preg_match('/^\d{10}$|^\d{13}$/', $this->isbn)) {
    throw new \InvalidArgumentException(
        'El ISBN debe tener exactamente 10 o 13 dígitos'
    );
}

        if (trim($this->title) === '') {
            throw new \InvalidArgumentException(
                'El título es obligatorio'
            );
        }

        if ($this->authorId <= 0) {
            throw new \InvalidArgumentException(
                'El autor es obligatorio'
            );
        }

        if ($this->totalCopies < 1) {
            throw new \InvalidArgumentException(
                'Debe existir al menos una copia'
            );
        }

        if ($this->availableCopies < 0) {
            throw new \InvalidArgumentException(
                'Las copias disponibles no pueden ser negativas'
            );
        }

        if ($this->availableCopies > $this->totalCopies) {
            throw new \InvalidArgumentException(
                'Las copias disponibles no pueden superar el total'
            );
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getPublicationYear(): ?int
    {
        return $this->publicationYear;
    }

    public function getTotalCopies(): int
    {
        return $this->totalCopies;
    }

    public function getAvailableCopies(): int
    {
        return $this->availableCopies;
    }
}