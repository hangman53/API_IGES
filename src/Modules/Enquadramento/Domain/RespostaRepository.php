<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento\Domain;

interface RespostaRepository
{
    public function notasDoUsuario(int $usuarioId): array;

    public function salvarInstrumento(int $usuarioId, int $instrumento, array $notas): void;
}
