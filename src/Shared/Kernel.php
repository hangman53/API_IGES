<?php
declare(strict_types=1);

namespace App\Shared;

use App\Shared\Auth\Autenticador;
use App\Shared\Database\Conexao;
use App\Shared\Database\Connection;
use App\Shared\Http\HttpException;
use App\Shared\Http\Request;
use App\Shared\Http\Response;
use App\Shared\Http\Router;
use App\Shared\Module\Module;
use PDO;
use Throwable;

final class Kernel
{
    private function __construct(
        private readonly Router $router,
        private readonly array $modulos,
    ) {
    }

    public static function criar(array $classesModulos): self
    {
        $container = new Container();
        $container->set(PDO::class, fn () => Connection::fromEnv());
        $container->set(Conexao::class, fn (Container $c) => new Conexao(fn () => $c->get(PDO::class)));
        $container->set(Autenticador::class, fn (Container $c) => new Autenticador($c->get(Conexao::class)));

        $modulos = array_map(fn (string $classe) => new $classe(), $classesModulos);

        foreach ($modulos as $modulo) {
            $modulo->registrar($container);
        }

        $router = new Router();
        $router->get('/health', fn () => Response::json(200, ['status' => 'ok']));
        foreach ($modulos as $modulo) {
            $modulo->rotas($router, $container);
        }

        return new self($router, $modulos);
    }

    public function modulos(): array
    {
        return $this->modulos;
    }

    public function handle(Request $request): Response
    {
        if ($request->method === 'OPTIONS') {
            return $this->comCors(Response::vazia(204));
        }

        try {
            $response = $this->router->dispatch($request);
        } catch (HttpException $e) {
            $response = Response::json($e->status, ['erro' => $e->getMessage()]);
        } catch (Throwable $e) {
            error_log(sprintf('[%s %s] %s', $request->method, $request->path, $e));
            $response = Response::json(500, ['erro' => 'Erro interno. Tente novamente mais tarde.']);
        }

        return $this->comCors($response);
    }

    private function comCors(Response $response): Response
    {
        return $response
            ->comHeader('Access-Control-Allow-Origin', getenv('CORS_ORIGIN') ?: 'http://localhost')
            ->comHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->comHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}
