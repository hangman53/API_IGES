<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Domain;

final class ResultadoTeste
{
    public function __construct(public readonly array $componentes)
    {
    }

    public function componente(Componente $componente): ResultadoComponente
    {
        foreach ($this->componentes as $resultado) {
            if ($resultado->componente === $componente) {
                return $resultado;
            }
        }
        throw new \LogicException("Componente ausente: {$componente->value}");
    }

    public function iges(bool $comExtras): float
    {
        $produto = 1.0;
        foreach ($this->componentes as $resultado) {
            $produto *= $resultado->minimoAtendido
                ? $resultado->nota($comExtras) ** $resultado->componente->peso()
                : 0.0;
        }
        return $produto;
    }

    public function notaMecanismo(Mecanismo $mecanismo, bool $comExtras): float
    {
        $notas = array_map(
            fn (Componente $c) => $this->componente($c)->nota($comExtras),
            $mecanismo->componentes(),
        );
        return array_sum($notas) / count($notas);
    }

    public function pendencias(): array
    {
        $pendencias = [];
        foreach ($this->componentes as $resultado) {
            if (!$resultado->minimoAtendido) {
                $pendencias[] = 'Não é possível calcular o iGES: falta instrumento mínimo do componente '
                    . $resultado->componente->nome();
            }
        }
        return $pendencias;
    }

    public function paraArray(): array
    {
        $calculavel = $this->pendencias() === [];

        $iges = fn (bool $comExtras) => [
            'valor'      => $this->iges($comExtras),
            'categoria'  => $calculavel ? Parametros::categoria($this->iges($comExtras)) : null,
            'calculavel' => $calculavel,
        ];

        return [
            'iges' => [
                'basico'           => $iges(false),
                'basicoMaisExtras' => $iges(true),
            ],
            'mecanismos' => array_map(fn (Mecanismo $m) => [
                'mecanismo'            => $m->value,
                'nome'                 => $m->nome(),
                'notaBasica'           => $this->notaMecanismo($m, false),
                'notaBasicoMaisExtras' => $this->notaMecanismo($m, true),
            ], Mecanismo::cases()),
            'componentes' => array_map(fn (ResultadoComponente $c) => $c->paraArray(), $this->componentes),
            'pendencias'  => $this->pendencias(),
        ];
    }
}
