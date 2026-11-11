<?php
declare(strict_types=1);

namespace Tests\Modules\Enquadramento;

use App\Modules\Enquadramento\Domain\CalculadoraEnquadramento;
use App\Modules\Enquadramento\Domain\Elemento;
use App\Modules\Enquadramento\Domain\ResultadoElemento;
use App\Modules\Enquadramento\Domain\ResultadoInstrumento;
use PHPUnit\Framework\TestCase;

final class CalculadoraEnquadramentoTest extends TestCase
{
    private CalculadoraEnquadramento $calculadora;

    protected function setUp(): void
    {
        $this->calculadora = new CalculadoraEnquadramento();
    }

    public function testTodasAsNotasMaximasDividemSempreSeteEEmpatamNoRank(): void
    {
        $notas     = array_fill_keys(Elemento::todosOsCodigos(), 4);
        $resultado = self::porElemento($this->calculadora->calcular(1, $notas));

        self::assertSame(28, $resultado['PO']->somatorio);
        self::assertSame(24, $resultado['CC']->somatorio);
        self::assertSame(20, $resultado['MA']->somatorio);

        self::assertEqualsWithDelta(4.0, $resultado['PO']->equivalencia, 1e-9);
        self::assertEqualsWithDelta(24 / 7, $resultado['GRCI']->equivalencia, 1e-9);
        self::assertEqualsWithDelta(20 / 7, $resultado['A']->equivalencia, 1e-9);

        self::assertSame(
            ['PO' => 1, 'I' => 1, 'COR' => 1, 'P' => 6, 'CC' => 4, 'GRCI' => 4, 'A' => 6, 'MA' => 6],
            array_map(fn (ResultadoElemento $e) => $e->rank, $resultado),
        );
    }

    public function testElementoZeradoFicaSemRank(): void
    {
        $notas = array_fill_keys(Elemento::todosOsCodigos(), 0);
        foreach (Elemento::P->codigos() as $codigo) {
            $notas[$codigo] = 1;
        }

        $resultado = self::porElemento($this->calculadora->calcular(2, $notas));

        self::assertSame(5, $resultado['P']->somatorio);
        self::assertSame(1, $resultado['P']->rank);
        foreach (['PO', 'I', 'COR', 'CC', 'GRCI', 'A', 'MA'] as $sigla) {
            self::assertSame(0, $resultado[$sigla]->somatorio);
            self::assertNull($resultado[$sigla]->rank, "$sigla deveria ficar sem rank");
        }
    }

    public function testRankDecrescenteComValoresDistintos(): void
    {
        $notas = array_fill_keys(Elemento::todosOsCodigos(), 0);
        $notas[111] = 3;
        $notas[121] = 4;
        $notas[122] = 2;
        $notas[311] = 1;
        $notas[331] = 3;

        $resultado = self::porElemento($this->calculadora->calcular(3, $notas));

        self::assertSame(1, $resultado['I']->rank);
        self::assertSame(2, $resultado['PO']->rank);
        self::assertSame(2, $resultado['MA']->rank);
        self::assertSame(4, $resultado['GRCI']->rank);
        self::assertNull($resultado['COR']->rank);
    }

    private static function porElemento(ResultadoInstrumento $resultado): array
    {
        $porSigla = [];
        foreach ($resultado->elementos as $elemento) {
            $porSigla[$elemento->elemento->value] = $elemento;
        }
        return $porSigla;
    }
}
