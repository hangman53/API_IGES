<?php
declare(strict_types=1);

namespace App\Shared\Http;

final class Router
{
    private array $rotas = [];

    public function get(string $caminho, callable $handler): void
    {
        $this->add('GET', $caminho, $handler);
    }

    public function post(string $caminho, callable $handler): void
    {
        $this->add('POST', $caminho, $handler);
    }

    public function put(string $caminho, callable $handler): void
    {
        $this->add('PUT', $caminho, $handler);
    }

    public function delete(string $caminho, callable $handler): void
    {
        $this->add('DELETE', $caminho, $handler);
    }

    public function add(string $metodo, string $caminho, callable $handler): void
    {
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', rtrim($caminho, '/') ?: '/');
        $this->rotas[] = ['metodo' => $metodo, 'regex' => "#^$regex$#", 'handler' => $handler];
    }

    public function dispatch(Request $request): Response
    {
        $caminhoExiste = false;

        foreach ($this->rotas as $rota) {
            if (!preg_match($rota['regex'], $request->path, $m)) {
                continue;
            }
            $caminhoExiste = true;
            if ($rota['metodo'] === $request->method) {
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                return ($rota['handler'])($request->comParams($params));
            }
        }

        throw $caminhoExiste
            ? new HttpException(405, 'Método não permitido')
            : HttpException::naoEncontrado('Rota não encontrada');
    }
}
