<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Domain;

final class Parametros
{
    public const FATOR_NIVEL_1      = 1.0;
    public const REDUCAO_POR_NIVEL  = 10.0;
    public const NIVEL_MAXIMO       = 8;
    public const MAXIMO_EXTRAS      = 10;

    public const FAIXAS = [
        ['categoria' => 'Excelente',  'acimaDe' => 90, 'ate' => 100],
        ['categoria' => 'Bom',        'acimaDe' => 70, 'ate' => 90],
        ['categoria' => 'Médio',      'acimaDe' => 50, 'ate' => 70],
        ['categoria' => 'Ruim',       'acimaDe' => 25, 'ate' => 50],
        ['categoria' => 'Muito ruim', 'acimaDe' => 0,  'ate' => 25],
    ];

    public static function fator(int $nivel): float
    {
        return self::FATOR_NIVEL_1 * (1 - self::REDUCAO_POR_NIVEL / 100) ** ($nivel - 1);
    }

    public static function categoria(float $iges): string
    {
        foreach (self::FAIXAS as $faixa) {
            if ($iges > $faixa['acimaDe']) {
                return $faixa['categoria'];
            }
        }
        return self::FAIXAS[array_key_last(self::FAIXAS)]['categoria'];
    }
}
