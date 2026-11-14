<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Domain;

final class ResultadoComponente
{
    public function __construct(
        public readonly Componente $componente,
        public readonly float $notaBasica,
        public readonly float $notaComExtras,
        public readonly int $basicosDoMunicipio,
        public readonly int $basicosTotal,
        public readonly int $extras,
        public readonly bool $minimoAtendido,
    ) {
    }

    public function nota(bool $comExtras): float
    {
        return $comExtras ? $this->notaComExtras : $this->notaBasica;
    }

    public function paraArray(): array
    {
        return [
            'componente'           => $this->componente->value,
            'nome'                 => $this->componente->nome(),
            'mecanismo'            => $this->componente->mecanismo()->value,
            'peso'                 => $this->componente->peso(),
            'notaBasica'           => $this->notaBasica,
            'notaBasicoMaisExtras' => $this->notaComExtras,
            'instrumentosBasicos'  => ['municipio' => $this->basicosDoMunicipio, 'total' => $this->basicosTotal],
            'instrumentosExtras'   => $this->extras,
            'minimoAtendido'       => $this->minimoAtendido,
        ];
    }
}
