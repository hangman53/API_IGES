<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento\Domain;

final class CalculadoraEnquadramento
{
    public const DIVISOR_EQUIVALENCIA = 7;

    public function calcular(int $instrumento, array $notas): ResultadoInstrumento
    {
        $somatorios = [];
        foreach (Elemento::cases() as $elemento) {
            $somatorios[$elemento->value] = array_sum(
                array_map(fn (int $codigo) => $notas[$codigo] ?? 0, $elemento->codigos())
            );
        }

        $resultados = [];
        foreach (Elemento::cases() as $elemento) {
            $somatorio    = $somatorios[$elemento->value];
            $resultados[] = new ResultadoElemento(
                $elemento,
                $somatorio,
                $somatorio / self::DIVISOR_EQUIVALENCIA,
                $somatorio === 0 ? null : self::rankEq($somatorio, $somatorios),
            );
        }

        return new ResultadoInstrumento($instrumento, $resultados);
    }

    private static function rankEq(int $valor, array $todos): int
    {
        return 1 + count(array_filter($todos, fn (int $outro) => $outro > $valor));
    }
}
