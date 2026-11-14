<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar;

use App\Modules\TestePreliminar\Api\TestePreliminarApi;
use App\Modules\TestePreliminar\Application\TestePreliminarService;
use App\Modules\TestePreliminar\Domain\CalculadoraIges;
use App\Modules\TestePreliminar\Domain\RespostasTesteRepository;
use App\Modules\TestePreliminar\Http\TestePreliminarController;
use App\Modules\TestePreliminar\Infrastructure\PdoRespostasTesteRepository;
use App\Shared\Auth\Autenticador;
use App\Shared\Container;
use App\Shared\Database\Conexao;
use App\Shared\Http\Router;
use App\Shared\Module\Module;

final class TestePreliminarModule implements Module
{
    public function nome(): string
    {
        return 'teste_preliminar';
    }

    public function registrar(Container $c): void
    {
        $c->set(RespostasTesteRepository::class, fn (Container $c) => new PdoRespostasTesteRepository($c->get(Conexao::class)));
        $c->set(TestePreliminarService::class, fn (Container $c) => new TestePreliminarService(
            $c->get(RespostasTesteRepository::class),
            new CalculadoraIges(),
        ));

        $c->set(TestePreliminarApi::class, fn (Container $c) => $c->get(TestePreliminarService::class));

        $c->set(TestePreliminarController::class, fn (Container $c) => new TestePreliminarController(
            $c->get(TestePreliminarService::class),
            $c->get(Autenticador::class),
        ));
    }

    public function rotas(Router $router, Container $c): void
    {
        $controller = fn () => $c->get(TestePreliminarController::class);

        $router->get('/teste-preliminar/configuracao', fn ($req) => $controller()->configuracao($req));
        $router->get('/teste-preliminar/respostas', fn ($req) => $controller()->listar($req));
        $router->post('/teste-preliminar/respostas', fn ($req) => $controller()->salvar($req));
        $router->get('/teste-preliminar/resultado', fn ($req) => $controller()->resultado($req));
    }

    public function caminhoMigrations(): string
    {
        return __DIR__ . '/migrations';
    }
}
