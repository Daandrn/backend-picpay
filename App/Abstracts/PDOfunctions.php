<?php declare(strict_types=1);

namespace App\Abstracts;

use DataBase\DataBase;
use PDO;
use PDOStatement;

abstract class PDOfunctions
{
    public PDO $pdo;

    public function __construct()
    {
        $this->pdo = DataBase::conn();
    }

    public function prepare(string $query, array $options = []): bool|PDOStatement
    {
        return $this->pdo->prepare($query, $options);
    }

    public function query(string $query, int|null $fetchMode = null): bool|PDOStatement
    {
        return $this->pdo->query($query, $fetchMode);
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
                
        return;
    }

    public function commit(): void
    {
        $this->pdo->commit();
                
        return;
    }

    public function rollback(): void
    {
        $this->pdo->commit();

        return;
    }
}
