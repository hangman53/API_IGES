<?php
declare(strict_types=1);

namespace App\Modules\Iges\Api;

interface IgesApi
{
    public function progresso(int $usuarioId): ProgressoDto;
}
