<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento\Api;

interface EnquadramentoApi
{
    public const TOTAL_INSTRUMENTOS = 23;

    public function notasDoUsuario(int $usuarioId): array;

    public function instrumentosRespondidos(int $usuarioId): array;

    public function resultadoDoUsuario(int $usuarioId): array;
}
