<?php
declare(strict_types=1);

namespace App\Shared\Database;

use Closure;
use PDO;

final class Conexao
{
    private ?PDO $pdo = null;

    public function __construct(private readonly Closure $fabrica)
    {
    }

    public function pdo(): PDO
    {
        return $this->pdo ??= ($this->fabrica)();
    }
}
