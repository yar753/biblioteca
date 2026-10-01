<?php

namespace App\Infrastructure\Persistence;

use App\Application\Port\TransactionManagerInterface;
use PDO;

class PdoTransactionManager implements TransactionManagerInterface
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function begin(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}