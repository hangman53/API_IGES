<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Infrastructure;

use App\Modules\TestePreliminar\Domain\RespostasTeste;
use App\Modules\TestePreliminar\Domain\RespostasTesteRepository;
use App\Shared\Database\Conexao;

final class PdoRespostasTesteRepository implements RespostasTesteRepository
{
    public function __construct(private readonly Conexao $conexao)
    {
    }

    public function buscar(int $usuarioId): ?RespostasTeste
    {
        $stmt = $this->conexao->pdo()->prepare('SELECT respostas FROM respostas_teste_preliminar WHERE usuario_id = :usuario');
        $stmt->execute([':usuario' => $usuarioId]);
        $json = $stmt->fetchColumn();

        return $json === false
            ? null
            : RespostasTeste::deArray(json_decode($json, true, flags: JSON_THROW_ON_ERROR));
    }

    public function salvar(int $usuarioId, RespostasTeste $respostas): void
    {
        $this->conexao->pdo()->prepare(
            'INSERT INTO respostas_teste_preliminar (usuario_id, respostas)
             VALUES (:usuario, CAST(:respostas AS JSONB))
             ON CONFLICT (usuario_id)
             DO UPDATE SET respostas = EXCLUDED.respostas, respondido_em = NOW()'
        )->execute([
            ':usuario'   => $usuarioId,
            ':respostas' => json_encode($respostas->paraArray(), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ]);
    }
}
