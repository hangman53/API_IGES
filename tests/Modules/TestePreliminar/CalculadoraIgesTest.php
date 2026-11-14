<?php
declare(strict_types=1);

namespace Tests\Modules\TestePreliminar;

use App\Modules\TestePreliminar\Domain\CalculadoraIges;
use App\Modules\TestePreliminar\Domain\CatalogoInstrumentos;
use App\Modules\TestePreliminar\Domain\Componente;
use App\Modules\TestePreliminar\Domain\InstrumentoExtra;
use App\Modules\TestePreliminar\Domain\Mecanismo;
use App\Modules\TestePreliminar\Domain\Parametros;
use App\Modules\TestePreliminar\Domain\RespostasInvalidas;
use App\Modules\TestePreliminar\Domain\RespostasTeste;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CalculadoraIgesTest extends TestCase
{
    private const DELTA = 1e-6;

    private static function municipioDaPlanilha(array $alteracoes = []): RespostasTeste
    {
        return RespostasTeste::deArray([...[
            'instrumentos' => [1, 2, 3, 4, 5, 6, 7, 11, 12, 16, 17],
            'minimos'      => ['PO', 'I', 'COR', 'P', 'CC', 'GR', 'A', 'MA'],
            'extras'       => [
                ['nome' => 'Instrumento extra 1', 'niveis' => ['PO' => 1, 'I' => 2, 'COR' => 3, 'P' => 4, 'CC' => 5, 'GR' => 6, 'A' => 7, 'MA' => 8]],
                ['nome' => 'Instrumento extra 2', 'niveis' => ['PO' => 1, 'I' => 4, 'COR' => 2, 'P' => 6, 'CC' => 3, 'GR' => 8, 'A' => 5, 'MA' => 7]],
            ],
        ], ...$alteracoes]);
    }

    public function testReproduzOMunicipioTesteDaPlanilha(): void
    {
        $resultado = (new CalculadoraIges())->calcular(self::municipioDaPlanilha());

        $esperado = [
            'PO'  => [54.32928339, 60.99065789, 8, 14],
            'I'   => [52.72410396, 57.91852152, 8, 15],
            'COR' => [41.85766259, 48.18006597, 7, 17],
            'P'   => [41.33160432, 49.51159815, 4, 10],
            'CC'  => [51.63624946, 55.61862152, 11, 21],
            'GR'  => [58.72683879, 63.03183199, 7, 13],
            'A'   => [43.33587266, 48.38684554, 7, 15],
            'MA'  => [50.6430572, 55.04905228, 7, 14],
        ];
        foreach ($esperado as $sigla => [$basica, $comExtras, $doMunicipio, $total]) {
            $componente = $resultado->componente(Componente::from($sigla));
            self::assertEqualsWithDelta($basica, $componente->notaBasica, self::DELTA, "nota básica $sigla");
            self::assertEqualsWithDelta($comExtras, $componente->notaComExtras, self::DELTA, "nota com extras $sigla");
            self::assertSame($doMunicipio, $componente->basicosDoMunicipio, "básicos do município $sigla");
            self::assertSame($total, $componente->basicosTotal, "básicos total $sigla");
        }

        self::assertEqualsWithDelta(48.6391983, $resultado->iges(false), self::DELTA);
        self::assertEqualsWithDelta(54.3372099, $resultado->iges(true), self::DELTA);

        self::assertEqualsWithDelta(49.63701665, $resultado->notaMecanismo(Mecanismo::ESTRATEGIA, false), self::DELTA);
        self::assertEqualsWithDelta(46.48392689, $resultado->notaMecanismo(Mecanismo::LIDERANCA, false), self::DELTA);
        self::assertEqualsWithDelta(50.90192288, $resultado->notaMecanismo(Mecanismo::CONTROLE, false), self::DELTA);
        self::assertEqualsWithDelta(55.69641512, $resultado->notaMecanismo(Mecanismo::ESTRATEGIA, true), self::DELTA);

        $array = $resultado->paraArray();
        self::assertSame('Ruim', $array['iges']['basico']['categoria']);
        self::assertSame('Médio', $array['iges']['basicoMaisExtras']['categoria']);
        self::assertSame([], $array['pendencias']);
    }

    public function testSemInstrumentoMinimoOIgesZeraEGeraPendencia(): void
    {
        $respostas = self::municipioDaPlanilha(['minimos' => ['PO', 'I', 'COR', 'P', 'CC', 'GR', 'A']]);
        $resultado = (new CalculadoraIges())->calcular($respostas);

        self::assertSame(0.0, $resultado->iges(false));
        self::assertCount(1, $resultado->pendencias());
        self::assertStringContainsString('Monitoramento e Avaliação', $resultado->pendencias()[0]);
        self::assertNull($resultado->paraArray()['iges']['basico']['categoria']);
        self::assertEqualsWithDelta(50.6430572, $resultado->componente(Componente::MA)->notaBasica, self::DELTA);
    }

    public function testInstrumentoCasoACasoDesconsideradoSaiDoCalculo(): void
    {
        $normal  = (new CalculadoraIges())->calcular(self::municipioDaPlanilha());
        $semPlano = (new CalculadoraIges())->calcular(self::municipioDaPlanilha(['desconsiderados' => [1]]));

        self::assertSame(14, $normal->componente(Componente::PO)->basicosTotal);
        self::assertSame(13, $semPlano->componente(Componente::PO)->basicosTotal);
        self::assertSame(7, $semPlano->componente(Componente::PO)->basicosDoMunicipio);
    }

    public function testFatoresCaem10PorCentoPorNivel(): void
    {
        self::assertEqualsWithDelta(1.0, Parametros::fator(1), self::DELTA);
        self::assertEqualsWithDelta(0.9, Parametros::fator(2), self::DELTA);
        self::assertEqualsWithDelta(0.4782969, Parametros::fator(8), self::DELTA);
    }

    public function testFaixasDeCategoria(): void
    {
        self::assertSame('Excelente', Parametros::categoria(90.01));
        self::assertSame('Bom', Parametros::categoria(90.0));
        self::assertSame('Médio', Parametros::categoria(50.5));
        self::assertSame('Ruim', Parametros::categoria(50.0));
        self::assertSame('Muito ruim', Parametros::categoria(25.0));
        self::assertSame('Muito ruim', Parametros::categoria(0.0));
    }

    public function testCatalogoRespeitaAsRegrasDeNivel(): void
    {
        foreach (CatalogoInstrumentos::BASICOS as $id => $instrumento) {
            InstrumentoExtra::validarNiveis($instrumento['niveis'], "instrumento básico $id");
        }
        $this->addToAssertionCount(1);
    }

    public static function respostasInvalidas(): array
    {
        return [
            'instrumento inexistente'       => [['instrumentos' => [99]]],
            'desconsiderar não caso a caso' => [['desconsiderados' => [2]]],
            'componente mínimo inválido'    => [['minimos' => ['XX']]],
            'extra sem nome'                => [['extras' => [['niveis' => ['PO' => 1]]]]],
            'extra com nível repetido'      => [['extras' => [['nome' => 'X', 'niveis' => ['PO' => 1, 'I' => 1]]]]],
            'extra pulando nível'           => [['extras' => [['nome' => 'X', 'niveis' => ['PO' => 1, 'I' => 3]]]]],
            'extra com nível 9'             => [['extras' => [['nome' => 'X', 'niveis' => ['PO' => 9]]]]],
            'mais de 10 extras'             => [['extras' => array_fill(0, 11, ['nome' => 'X', 'niveis' => ['PO' => 1]])]],
        ];
    }

    #[DataProvider('respostasInvalidas')]
    public function testRejeitaRespostasInvalidas(array $dados): void
    {
        $this->expectException(RespostasInvalidas::class);
        RespostasTeste::deArray($dados);
    }
}
