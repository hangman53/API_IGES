<?php
declare(strict_types=1);

namespace App\Modules\Iges\Api;

use JsonSerializable;

final class ProgressoDto implements JsonSerializable
{
    public function __construct(
        public readonly array $instrumentosRespondidos,
        public readonly int $totalInstrumentos,
    ) {
    }

    public function percentual(): float
    {
        return $this->totalInstrumentos === 0
            ? 0.0
            : round(count($this->instrumentosRespondidos) / $this->totalInstrumentos * 100, 1);
    }

    public function jsonSerialize(): array
    {
        return [
            'instrumentosRespondidos' => $this->instrumentosRespondidos,
            'totalInstrumentos'       => $this->totalInstrumentos,
            'percentual'              => $this->percentual(),
        ];
    }
}
