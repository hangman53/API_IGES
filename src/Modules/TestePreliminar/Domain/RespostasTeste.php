<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Domain;

final class RespostasTeste
{
    private function __construct(
        private readonly array $instrumentos,
        private readonly array $desconsiderados,
        private readonly array $minimos,
        public readonly array $extras,
    ) {
    }

    public static function deArray(mixed $dados): self
    {
        if (!is_array($dados)) {
            throw new RespostasInvalidas('Respostas inválidas');
        }

        $instrumentos = self::listaDeIds($dados['instrumentos'] ?? [], 'instrumentos');
        foreach ($instrumentos as $id) {
            if (!CatalogoInstrumentos::existe($id)) {
                throw new RespostasInvalidas("Instrumento básico inexistente: $id");
            }
        }

        $desconsiderados = self::listaDeIds($dados['desconsiderados'] ?? [], 'desconsiderados');
        foreach ($desconsiderados as $id) {
            if (!CatalogoInstrumentos::ehCasoACaso($id)) {
                throw new RespostasInvalidas("Só instrumentos caso a caso podem ser desconsiderados: $id");
            }
        }

        $minimos = $dados['minimos'] ?? [];
        if (!is_array($minimos) || !array_is_list($minimos)) {
            throw new RespostasInvalidas('O campo minimos deve ser uma lista de componentes');
        }
        $minimos = array_map(
            fn ($sigla) => (is_string($sigla) ? Componente::tryFrom($sigla) : null)
                ?? throw new RespostasInvalidas('Componente inválido em minimos: ' . json_encode($sigla)),
            $minimos,
        );

        $extras = $dados['extras'] ?? [];
        if (!is_array($extras) || !array_is_list($extras)) {
            throw new RespostasInvalidas('O campo extras deve ser uma lista');
        }
        if (count($extras) > Parametros::MAXIMO_EXTRAS) {
            throw new RespostasInvalidas('São permitidos no máximo ' . Parametros::MAXIMO_EXTRAS . ' instrumentos extras');
        }
        $extras = array_map(
            fn ($extra, int $i) => InstrumentoExtra::deArray($extra, $i + 1),
            $extras,
            array_keys($extras),
        );

        return new self(
            $instrumentos,
            $desconsiderados,
            array_values(array_unique(array_map(fn (Componente $c) => $c->value, $minimos))),
            $extras,
        );
    }

    private static function listaDeIds(mixed $valores, string $campo): array
    {
        if (!is_array($valores) || !array_is_list($valores)) {
            throw new RespostasInvalidas("O campo $campo deve ser uma lista de ids");
        }

        $ids = [];
        foreach ($valores as $valor) {
            $id = filter_var($valor, FILTER_VALIDATE_INT);
            if ($id === false) {
                throw new RespostasInvalidas("Id inválido em $campo: " . json_encode($valor));
            }
            $ids[$id] = $id;
        }
        sort($ids);
        return array_values($ids);
    }

    public function possui(int $id): bool
    {
        return in_array($id, $this->instrumentos, true);
    }

    public function desconsiderou(int $id): bool
    {
        return in_array($id, $this->desconsiderados, true);
    }

    public function atendeMinimo(Componente $componente): bool
    {
        return in_array($componente->value, $this->minimos, true);
    }

    public function paraArray(): array
    {
        return [
            'instrumentos'    => $this->instrumentos,
            'desconsiderados' => $this->desconsiderados,
            'minimos'         => $this->minimos,
            'extras'          => array_map(fn (InstrumentoExtra $e) => $e->paraArray(), $this->extras),
        ];
    }
}
