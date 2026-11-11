<?php
declare(strict_types=1);

namespace App\Modules\Iges;

use App\Modules\Enquadramento\Api\EnquadramentoApi;
use App\Modules\Iges\Api\IgesApi;
use App\Modules\Iges\Application\IgesService;
use App\Modules\Iges\Http\IgesController;
use App\Shared\Auth\Autenticador;
use App\Shared\Container;
use App\Shared\Http\Router;
use App\Shared\Module\Module;

final class IgesModule implements Module
{
    public function nome(): string
    {
        return 'iges';
    }

    public function registrar(Container $c): void
    {
        $c->set(IgesService::class, fn (Container $c) => new IgesService($c->get(EnquadramentoApi::class)));

        $c->set(IgesApi::class, fn (Container $c) => $c->get(IgesService::class));

        $c->set(IgesController::class, fn (Container $c) => new IgesController(
            $c->get(IgesService::class),
            $c->get(Autenticador::class),
        ));
    }

    public function rotas(Router $router, Container $c): void
    {
        $controller = fn () => $c->get(IgesController::class);

        $router->get('/iges/progresso', fn ($req) => $controller()->progresso($req));
    }

    public function caminhoMigrations(): string
    {
        return __DIR__ . '/migrations';
    }
}
