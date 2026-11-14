<?php
declare(strict_types=1);

namespace App\Modules\TestePreliminar\Application;

use App\Modules\TestePreliminar\Api\TestePreliminarApi;
use App\Modules\TestePreliminar\Domain\CalculadoraIges;
use App\Modules\TestePreliminar\Domain\CatalogoInstrumentos;
use App\Modules\TestePreliminar\Domain\Componente;
use App\Modules\TestePreliminar\Domain\Mecanismo;
use App\Modules\TestePreliminar\Domain\Parametros;
use App\Modules\TestePreliminar\Domain\RespostasTeste;
use App\Modules\TestePreliminar\Domain\RespostasTesteRepository;
use App\Modules\TestePreliminar\Domain\ResultadoTeste;

final class TestePreliminarService implements TestePreliminarApi
{
    public function __construct(
        private readonly RespostasTesteRepository $respostas,
        private readonly CalculadoraIges $calculadora,
    ) {
    }

    public function configuracao(): array
    {
        $fatores = [];
        for ($nivel = 1; $nivel <= Parametros::NIVEL_MAXIMO; $nivel++) {
            $fatores[] = ['nivel' => $nivel, 'fator' => Parametros::fator($nivel)];
        }

        return [
            'parametros' => [
                'fatorNivel1'     => Parametros::FATOR_NIVEL_1,
                'reducaoPorNivel' => Parametros::REDUCAO_POR_NIVEL,
                'nivelMaximo'     => Parametros::NIVEL_MAXIMO,
                'maximoExtras'    => Parametros::MAXIMO_EXTRAS,
                'fatores'         => $fatores,
            ],
            'faixas'     => Parametros::FAIXAS,
            'mecanismos' => array_map(fn (Mecanismo $m) => [
                'mecanismo'   => $m->value,
                'nome'        => $m->nome(),
                'componentes' => array_map(fn (Componente $c) => $c->value, $m->componentes()),
            ], Mecanismo::cases()),
            'componentes' => array_map(fn (Componente $c) => [
                'componente'          => $c->value,
                'nome'                => $c->nome(),
                'mecanismo'           => $c->mecanismo()->value,
                'peso'                => $c->peso(),
                'instrumentosMinimos' => $c->instrumentosMinimos(),
            ], Componente::cases()),
            'instrumentosBasicos' => CatalogoInstrumentos::paraArray(),
        ];
    }

    public function respostasDoUsuario(int $usuarioId): ?RespostasTeste
    {
        return $this->respostas->buscar($usuarioId);
    }

    public function salvarRespostas(int $usuarioId, mixed $dados): ResultadoTeste
    {
        $respostas = RespostasTeste::deArray($dados);
        $this->respostas->salvar($usuarioId, $respostas);
        return $this->calculadora->calcular($respostas);
    }

    public function calcularResultado(int $usuarioId): ?ResultadoTeste
    {
        $respostas = $this->respostas->buscar($usuarioId);
        return $respostas === null ? null : $this->calculadora->calcular($respostas);
    }

    public function resultadoDoUsuario(int $usuarioId): ?array
    {
        return $this->calcularResultado($usuarioId)?->paraArray();
    }
}
