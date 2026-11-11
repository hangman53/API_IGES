<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento\Domain;

final class ResultadoElemento
{
    public function __construct(
        public readonly Elemento $elemento,
        public readonly int $somatorio,
        public readonly float $equivalencia,
        public readonly ?int $rank,
    ) {
    }

    public function paraArray(): array
    {
        return [
            'elemento'     => $this->elemento->value,
            'somatorio'    => $this->somatorio,
            'equivalencia' => $this->equivalencia,
            'rank'         => $this->rank,
        ];
    }
}
