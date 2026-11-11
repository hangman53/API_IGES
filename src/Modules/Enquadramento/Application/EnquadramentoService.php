<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento\Application;

use App\Modules\Enquadramento\Api\EnquadramentoApi;
use App\Modules\Enquadramento\Domain\CalculadoraEnquadramento;
use App\Modules\Enquadramento\Domain\Questionario;
use App\Modules\Enquadramento\Domain\RespostaRepository;
use App\Modules\Enquadramento\Domain\ResultadoInstrumento;

final class EnquadramentoService implements EnquadramentoApi
{
    public function __construct(
        private readonly RespostaRepository $respostas,
        private readonly CalculadoraEnquadramento $calculadora,
    ) {
    }

    public function notasDoUsuario(int $usuarioId): array
    {
        return $this->respostas->notasDoUsuario($usuarioId);
    }

    public function instrumentosRespondidos(int $usuarioId): array
    {
        $instrumentos = array_keys($this->respostas->notasDoUsuario($usuarioId));
        sort($instrumentos);
        return $instrumentos;
    }

    public function resultadoDoUsuario(int $usuarioId): array
    {
        return array_map(
            fn (ResultadoInstrumento $r) => $r->paraArray(),
            $this->calcularResultado($usuarioId),
        );
    }

    public function calcularResultado(int $usuarioId): array
    {
        $notas = $this->respostas->notasDoUsuario($usuarioId);
        ksort($notas);

        $resultados = [];
        foreach ($notas as $instrumento => $notasInstrumento) {
            $resultados[] = $this->calculadora->calcular($instrumento, $notasInstrumento);
        }
        return $resultados;
    }

    public function salvarRespostas(int $usuarioId, mixed $instrumento, mixed $respostas): ResultadoInstrumento
    {
        $instrumento = Questionario::validarInstrumento($instrumento);
        $notas       = Questionario::validarNotas($respostas);

        $this->respostas->salvarInstrumento($usuarioId, $instrumento, $notas);
        return $this->calculadora->calcular($instrumento, $notas);
    }
}
