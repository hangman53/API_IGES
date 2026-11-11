<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Shared\Database\Connection;
use App\Shared\Database\Migrator;
use App\Shared\Kernel;

$kernel   = Kernel::criar(require __DIR__ . '/../config/modules.php');
$migrator = new Migrator(Connection::fromEnv());

$migrator->migrar('shared', __DIR__ . '/../src/Shared/Auth/migrations');
foreach ($kernel->modulos() as $modulo) {
    $migrator->migrar($modulo->nome(), $modulo->caminhoMigrations());
}
