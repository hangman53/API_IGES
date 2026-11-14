<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Api;

interface TestePreliminarApi
{
    public function resultadoDoUsuario(int $usuarioId): ?array;
}
