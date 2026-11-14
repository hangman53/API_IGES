<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Domain;

final class InstrumentoExtra
{
    private function __construct(
        public readonly string $nome,
        private readonly array $niveis,
    ) {
    }

    public static function deArray(mixed $dados, int $posicao): self
    {
        if (!is_array($dados)) {
            throw new RespostasInvalidas("Instrumento extra $posicao inválido");
        }

        $nome = is_string($dados['nome'] ?? null) ? trim($dados['nome']) : '';
        if (preg_match('/^.{1,255}$/su', $nome) !== 1) {
            throw new RespostasInvalidas("Informe o nome do instrumento extra $posicao (até 255 caracteres)");
        }

        return new self($nome, self::validarNiveis($dados['niveis'] ?? null, "instrumento extra \"$nome\""));
    }

    public static function validarNiveis(mixed $niveis, string $descricao): array
    {
        if (!is_array($niveis) || $niveis === []) {
            throw new RespostasInvalidas("Informe ao menos um nível para o $descricao");
        }

        $validos = [];
        foreach ($niveis as $sigla => $nivel) {
            $componente = Componente::tryFrom((string) $sigla)
                ?? throw new RespostasInvalidas("Componente inválido no $descricao: $sigla");

            $valor = filter_var($nivel, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => Parametros::NIVEL_MAXIMO],
            ]);
            if ($valor === false) {
                throw new RespostasInvalidas("Nível do componente $sigla no $descricao deve ser de 1 a " . Parametros::NIVEL_MAXIMO);
            }
            $validos[$componente->value] = $valor;
        }

        $valores = array_values($validos);
        sort($valores);
        if (count(array_unique($valores)) !== count($valores)) {
            throw new RespostasInvalidas("O $descricao não pode ter o mesmo nível em componentes diferentes");
        }
        if ($valores !== range(1, count($valores))) {
            throw new RespostasInvalidas("Os níveis do $descricao devem começar em 1 e não podem pular números");
        }

        return $validos;
    }

    public function nivel(Componente $componente): ?int
    {
        return $this->niveis[$componente->value] ?? null;
    }

    public function paraArray(): array
    {
        return ['nome' => $this->nome, 'niveis' => $this->niveis];
    }
}
