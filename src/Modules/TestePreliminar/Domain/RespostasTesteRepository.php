<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Domain;

interface RespostasTesteRepository
{
    public function buscar(int $usuarioId): ?RespostasTeste;

    public function salvar(int $usuarioId, RespostasTeste $respostas): void;
}
