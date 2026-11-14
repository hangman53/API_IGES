<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento\Infrastructure;

use App\Modules\Enquadramento\Domain\RespostaRepository;
use App\Shared\Database\Conexao;
use Throwable;

final class PdoRespostaRepository implements RespostaRepository
{
    public function __construct(private readonly Conexao $conexao)
    {
    }

    public function notasDoUsuario(int $usuarioId): array
    {
        $stmt = $this->conexao->pdo()->prepare(
            'SELECT instrumento, codigo_pergunta, nota
               FROM respostas_enquadramento
              WHERE usuario_id = :usuario'
        );
        $stmt->execute([':usuario' => $usuarioId]);

        $notas = [];
        foreach ($stmt as $linha) {
            $notas[(int) $linha['instrumento']][(int) $linha['codigo_pergunta']] = (int) $linha['nota'];
        }
        return $notas;
    }

    public function salvarInstrumento(int $usuarioId, int $instrumento, array $notas): void
    {
        $pdo = $this->conexao->pdo();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO respostas_enquadramento (usuario_id, instrumento, codigo_pergunta, nota)
                 VALUES (:usuario, :instrumento, :codigo, :nota)
                 ON CONFLICT (usuario_id, instrumento, codigo_pergunta)
                 DO UPDATE SET nota = EXCLUDED.nota, respondido_em = NOW()'
            );
            foreach ($notas as $codigo => $nota) {
                $stmt->execute([
                    ':usuario'     => $usuarioId,
                    ':instrumento' => $instrumento,
                    ':codigo'      => $codigo,
                    ':nota'        => $nota,
                ]);
            }
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
