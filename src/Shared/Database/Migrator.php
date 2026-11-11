<?php
declare(strict_types=1);

namespace App\Shared\Database;

use PDO;
use Throwable;

final class Migrator
{
    public function __construct(private readonly PDO $pdo)
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                modulo      VARCHAR(100) NOT NULL,
                arquivo     VARCHAR(255) NOT NULL,
                aplicada_em TIMESTAMP    NOT NULL DEFAULT NOW(),
                PRIMARY KEY (modulo, arquivo)
            )'
        );
    }

    public function migrar(string $modulo, string $pasta): void
    {
        $arquivos = glob(rtrim($pasta, '/') . '/*.sql') ?: [];
        sort($arquivos);

        $stmt = $this->pdo->prepare('SELECT arquivo FROM schema_migrations WHERE modulo = :modulo');
        $stmt->execute([':modulo' => $modulo]);
        $jaAplicadas = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($arquivos as $caminho) {
            $arquivo = basename($caminho);
            if (in_array($arquivo, $jaAplicadas, true)) {
                continue;
            }

            $this->pdo->beginTransaction();
            try {
                $this->pdo->exec(file_get_contents($caminho));
                $this->pdo->prepare('INSERT INTO schema_migrations (modulo, arquivo) VALUES (:modulo, :arquivo)')
                    ->execute([':modulo' => $modulo, ':arquivo' => $arquivo]);
                $this->pdo->commit();
                echo "[$modulo] $arquivo aplicada" . PHP_EOL;
            } catch (Throwable $e) {
                $this->pdo->rollBack();
                throw $e;
            }
        }
    }
}
