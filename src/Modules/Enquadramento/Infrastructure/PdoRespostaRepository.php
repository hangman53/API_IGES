<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento\Infrastructure;

use App\Modules\Enquadramento\Domain\RespostaRepository;
use PDO;
use Throwable;

final class PdoRespostaRepository implements RespostaRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function notasDoUsuario(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
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
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
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
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
