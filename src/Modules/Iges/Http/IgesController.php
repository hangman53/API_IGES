<?php
declare(strict_types=1);

namespace App\Modules\Iges\Http;

use App\Modules\Iges\Application\IgesService;
use App\Shared\Auth\Autenticador;
use App\Shared\Http\Request;
use App\Shared\Http\Response;

final class IgesController
{
    public function __construct(
        private readonly IgesService $service,
        private readonly Autenticador $auth,
    ) {
    }

    public function progresso(Request $request): Response
    {
        $usuario = $this->auth->exigirUsuario($request);

        return Response::json(200, $this->service->progresso($usuario->id));
    }
}
