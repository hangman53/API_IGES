<?php
declare(strict_types=1);

namespace App\Modules\Enquadramento\Domain;

final class ResultadoInstrumento
{
    public function __construct(
        public readonly int $instrumento,
        public readonly array $elementos,
    ) {
    }

    public function paraArray(): array
    {
        return [
            'instrumento' => $this->instrumento,
            'nome'        => Questionario::nomeInstrumento($this->instrumento),
            'elementos'   => array_map(fn (ResultadoElemento $e) => $e->paraArray(), $this->elementos),
        ];
    }
}
