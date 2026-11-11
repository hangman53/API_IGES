<?php
declare(strict_types=1);

namespace App\Shared\Module;

use App\Shared\Container;
use App\Shared\Http\Router;

interface Module
{
    public function nome(): string;

    public function registrar(Container $container): void;

    public function rotas(Router $router, Container $container): void;

    public function caminhoMigrations(): string;
}
