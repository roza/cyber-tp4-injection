<?php

namespace Kineo\Tests;

use Kineo\Schema;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Chaque test repart d'une base neuve : SQLite en mémoire par défaut, ou la
 * base désignée par DB_DSN (MariaDB / MySQL, voir LISEZMOI.md).
 */
abstract class BaseTestCase extends TestCase
{
    protected PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO(
            getenv('DB_DSN') ?: 'sqlite::memory:',
            getenv('DB_USER') ?: null,
            getenv('DB_PASS') ?: null
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        Schema::initialiser($this->pdo);
    }
}
