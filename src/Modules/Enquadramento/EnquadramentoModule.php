<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento;

use App\Modules\Enquadramento\Api\EnquadramentoApi;
use App\Modules\Enquadramento\Application\EnquadramentoService;
use App\Modules\Enquadramento\Domain\CalculadoraEnquadramento;
use App\Modules\Enquadramento\Domain\RespostaRepository;
use App\Modules\Enquadramento\Http\EnquadramentoController;
use App\Modules\Enquadramento\Infrastructure\PdoRespostaRepository;
use App\Shared\Auth\Autenticador;
use App\Shared\Container;
use App\Shared\Http\Router;
use App\Shared\Module\Module;
use PDO;

final class EnquadramentoModule implements Module
{
    public function nome(): string
    {
        return 'enquadramento';
    }

    public function registrar(Container $c): void
    {
        $c->set(RespostaRepository::class, fn (Container $c) => new PdoRespostaRepository($c->get(PDO::class)));
        $c->set(EnquadramentoService::class, fn (Container $c) => new EnquadramentoService(
            $c->get(RespostaRepository::class),
            new CalculadoraEnquadramento(),
        ));

        $c->set(EnquadramentoApi::class, fn (Container $c) => $c->get(EnquadramentoService::class));

        $c->set(EnquadramentoController::class, fn (Container $c) => new EnquadramentoController(
            $c->get(EnquadramentoService::class),
            $c->get(Autenticador::class),
        ));
    }

    public function rotas(Router $router, Container $c): void
    {
        $controller = fn () => $c->get(EnquadramentoController::class);

        $router->get('/enquadramento/questionario', fn ($req) => $controller()->questionario($req));
        $router->get('/enquadramento/respostas', fn ($req) => $controller()->listar($req));
        $router->post('/enquadramento/respostas', fn ($req) => $controller()->salvar($req));
        $router->get('/enquadramento/resultado', fn ($req) => $controller()->resultado($req));
    }

    public function caminhoMigrations(): string
    {
        return __DIR__ . '/migrations';
    }
}
