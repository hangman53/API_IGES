<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento\Http;

use App\Modules\Enquadramento\Application\EnquadramentoService;
use App\Modules\Enquadramento\Domain\Questionario;
use App\Modules\Enquadramento\Domain\RespostasInvalidas;
use App\Shared\Auth\Autenticador;
use App\Shared\Http\HttpException;
use App\Shared\Http\Request;
use App\Shared\Http\Response;

final class EnquadramentoController
{
    public function __construct(
        private readonly EnquadramentoService $service,
        private readonly Autenticador $auth,
    ) {
    }

    public function questionario(Request $request): Response
    {
        return Response::json(200, Questionario::paraArray());
    }

    public function listar(Request $request): Response
    {
        $usuario = $this->auth->exigirUsuario($request);

        return Response::json(200, ['respostas' => (object) $this->service->notasDoUsuario($usuario->id)]);
    }

    public function salvar(Request $request): Response
    {
        $usuario = $this->auth->exigirUsuario($request);
        $body    = $request->body();

        try {
            $resultado = $this->service->salvarRespostas(
                $usuario->id,
                $body['instrumento'] ?? null,
                $body['respostas'] ?? null,
            );
        } catch (RespostasInvalidas $e) {
            throw HttpException::badRequest($e->getMessage());
        }

        return Response::json(200, ['mensagem' => 'Respostas salvas', 'resultado' => $resultado->paraArray()]);
    }

    public function resultado(Request $request): Response
    {
        $usuario = $this->auth->exigirUsuario($request);

        return Response::json(200, ['resultado' => $this->service->resultadoDoUsuario($usuario->id)]);
    }
}
