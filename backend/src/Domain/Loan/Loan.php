<?php

namespace App\Domain\Loan;

class Loan
{
    public function __construct(
        private ?int $id,
        private int $bookId,
        private int $memberId,
        private string $loanDate,
        private string $dueDate,
        private ?string $returnDate,
        private string $status = 'ACTIVE'
    ) {
        if ($this->bookId <= 0) {
            throw new \InvalidArgumentException(
                'El libro es obligatorio'
            );
        }

        if ($this->memberId <= 0) {
            throw new \InvalidArgumentException(
                'El miembro es obligatorio'
            );
        }

        if (trim($this->loanDate) === '') {
            throw new \InvalidArgumentException(
                'La fecha de préstamo es obligatoria'
            );
        }

        if (trim($this->dueDate) === '') {
            throw new \InvalidArgumentException(
                'La fecha de devolución es obligatoria'
            );
        }

        if (!in_array($this->status, ['ACTIVE', 'RETURNED'], true)) {
            throw new \InvalidArgumentException(
                'El estado del préstamo no es válido'
            );
        }

        if ($this->status === 'RETURNED' && $this->returnDate === null) {
            throw new \InvalidArgumentException(
                'Un préstamo devuelto debe tener fecha de devolución'
            );
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookId(): int
    {
        return $this->bookId;
    }

    public function getMemberId(): int
    {
        return $this->memberId;
    }

    public function getLoanDate(): string
    {
        return $this->loanDate;
    }

    public function getDueDate(): string
    {
        return $this->dueDate;
    }

    public function getReturnDate(): ?string
    {
        return $this->returnDate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}