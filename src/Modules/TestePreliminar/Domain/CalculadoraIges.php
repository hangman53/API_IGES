<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Domain;

final class CalculadoraIges
{
    public function calcular(RespostasTeste $respostas): ResultadoTeste
    {
        $componentes = [];
        foreach (Componente::cases() as $componente) {
            $componentes[] = $this->calcularComponente($componente, $respostas);
        }
        return new ResultadoTeste($componentes);
    }

    private function calcularComponente(Componente $componente, RespostasTeste $respostas): ResultadoComponente
    {
        $maximo        = 0.0;
        $doMunicipio   = 0.0;
        $basicosTotal  = 0;
        $basicosMun    = 0;
        foreach (array_keys(CatalogoInstrumentos::BASICOS) as $id) {
            $nivel = CatalogoInstrumentos::nivel($id, $componente);
            if ($nivel === null || $respostas->desconsiderou($id)) {
                continue;
            }
            $fator   = Parametros::fator($nivel);
            $maximo += $fator;
            $basicosTotal++;
            if ($respostas->possui($id)) {
                $doMunicipio += $fator;
                $basicosMun++;
            }
        }

        $extras    = 0.0;
        $qtdExtras = 0;
        foreach ($respostas->extras as $extra) {
            $nivel = $extra->nivel($componente);
            if ($nivel !== null) {
                $extras += Parametros::fator($nivel);
                $qtdExtras++;
            }
        }

        return new ResultadoComponente(
            $componente,
            self::nota($doMunicipio, $maximo),
            self::nota($doMunicipio + $extras, $maximo + $extras),
            $basicosMun,
            $basicosTotal,
            $qtdExtras,
            $respostas->atendeMinimo($componente),
        );
    }

    private static function nota(float $obtido, float $maximo): float
    {
        return $maximo > 0 ? 100 * $obtido / $maximo : 0.0;
    }
}
