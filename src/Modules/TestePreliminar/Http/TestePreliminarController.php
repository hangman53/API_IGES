<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Http;

use App\Modules\TestePreliminar\Application\TestePreliminarService;
use App\Modules\TestePreliminar\Domain\RespostasInvalidas;
use App\Shared\Auth\Autenticador;
use App\Shared\Http\HttpException;
use App\Shared\Http\Request;
use App\Shared\Http\Response;

final class TestePreliminarController
{
    public function __construct(
        private readonly TestePreliminarService $service,
        private readonly Autenticador $auth,
    ) {
    }

    public function configuracao(Request $request): Response
    {
        return Response::json(200, $this->service->configuracao());
    }

    public function listar(Request $request): Response
    {
        $usuario   = $this->auth->exigirUsuario($request);
        $respostas = $this->service->respostasDoUsuario($usuario->id);

        return Response::json(200, ['respostas' => $respostas?->paraArray()]);
    }

    public function salvar(Request $request): Response
    {
        $usuario = $this->auth->exigirUsuario($request);

        try {
            $resultado = $this->service->salvarRespostas($usuario->id, $request->body());
        } catch (RespostasInvalidas $e) {
            throw HttpException::badRequest($e->getMessage());
        }

        return Response::json(200, ['mensagem' => 'Respostas salvas', 'resultado' => $resultado->paraArray()]);
    }

    public function resultado(Request $request): Response
    {
        $usuario   = $this->auth->exigirUsuario($request);
        $resultado = $this->service->resultadoDoUsuario($usuario->id)
            ?? throw HttpException::naoEncontrado('Teste preliminar ainda não respondido');

        return Response::json(200, ['resultado' => $resultado]);
    }
}
