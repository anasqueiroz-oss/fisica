<?php

declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use Model\AguaAmostra;
use Model\Biofiltro;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Biofiltro::class)]
final class BiofiltroTest extends TestCase
{
    private function adequada(): AguaAmostra
    {
        return new AguaAmostra(7.0, 1.0, 0.5, 100.0, 25.0);
    }

    private function inadequada(): AguaAmostra
    {
        return new AguaAmostra(5.0, 8.0, 0.1, 400.0, 25.0);
    }

    #[DataProvider('eficienciasProvider')]
    public function testCalculoDaEficiencia(float $antes, float $depois, ?float $esperado): void
    {
        $resultado = Biofiltro::calcularEficiencia($antes, $depois);

        if ($esperado === null) {
            $this->assertNull($resultado);
        } else {
            $this->assertEqualsWithDelta($esperado, $resultado, 0.001);
        }
    }

    public static function eficienciasProvider(): array
    {
        return [
            'remocao de 80%' => [10.0, 2.0, 80.0],
            'remocao total' => [3.0, 0.0, 100.0],
            'sem alteracao' => [4.0, 4.0, 0.0],
            'aumento do parametro' => [5.0, 10.0, -100.0],
            'antes zero e depois zero' => [0.0, 0.0, null],
            'antes zero (divisao por zero)' => [0.0, 3.0, null],
        ];
    }

    #[DataProvider('entradasInvalidasProvider')]
    public function testEficienciaComEntradaInvalidaLancaExcecao(float $antes, float $depois): void
    {
        $this->expectException(InvalidArgumentException::class);

        Biofiltro::calcularEficiencia($antes, $depois);
    }

    public static function entradasInvalidasProvider(): array
    {
        return [
            'antes negativo' => [-1.0, 2.0],
            'depois negativo' => [2.0, -1.0],
            'antes NaN' => [NAN, 2.0],
            'depois infinito' => [2.0, INF],
        ];
    }

    public function testCompararCalculaEficienciaVariacaoEMarcaOsRemoviveis(): void
    {
        $antes = new AguaAmostra(7.0, 10.0, 1.0, 200.0, 25.0);
        $depois = new AguaAmostra(7.2, 2.0, 0.5, 150.0, 24.0);

        $itens = [];
        foreach ((new Biofiltro($antes, $depois))->comparar() as $item) {
            $itens[$item['chave']] = $item;
        }

        $this->assertCount(5, $itens);
        $this->assertEqualsWithDelta(80.0, $itens['turbidez']['eficiencia'], 0.001);
        $this->assertEqualsWithDelta(50.0, $itens['cloro']['eficiencia'], 0.001);
        $this->assertEqualsWithDelta(25.0, $itens['dureza']['eficiencia'], 0.001);
        $this->assertTrue($itens['turbidez']['removivel']);

        $this->assertNull($itens['ph']['eficiencia']);
        $this->assertFalse($itens['ph']['removivel']);
        $this->assertEqualsWithDelta(0.2, $itens['ph']['variacao'], 0.001);
        $this->assertNull($itens['temperatura']['eficiencia']);
        $this->assertEqualsWithDelta(-1.0, $itens['temperatura']['variacao'], 0.001);
    }

    public function testCompararComValorInicialZeroNaoDividePorZero(): void
    {
        $antes = new AguaAmostra(7.0, 0.0, 0.5, 100.0, 25.0);
        $depois = new AguaAmostra(7.0, 0.0, 0.5, 100.0, 25.0);

        $itens = [];
        foreach ((new Biofiltro($antes, $depois))->comparar() as $item) {
            $itens[$item['chave']] = $item;
        }

        $this->assertNull($itens['turbidez']['eficiencia']);
    }

    public function testGettersRetornamAsAmostras(): void
    {
        $antes = $this->inadequada();
        $depois = $this->adequada();
        $biofiltro = new Biofiltro($antes, $depois);

        $this->assertSame($antes, $biofiltro->getAntes());
        $this->assertSame($depois, $biofiltro->getDepois());
    }

    #[DataProvider('situacoesProvider')]
    public function testSituacaoComparaAdequacaoAntesEDepois(bool $antesAdequada, bool $depoisAdequada, string $esperada): void
    {
        $antes = $antesAdequada ? $this->adequada() : $this->inadequada();
        $depois = $depoisAdequada ? $this->adequada() : $this->inadequada();

        $biofiltro = new Biofiltro($antes, $depois);

        $this->assertSame($esperada, $biofiltro->situacao());
        $this->assertNotSame('', $biofiltro->descricaoSituacao());
    }

    public static function situacoesProvider(): array
    {
        return [
            'adequada -> adequada' => [true, true, 'manteve_adequada'],
            'inadequada -> adequada' => [false, true, 'tornou_adequada'],
            'adequada -> inadequada' => [true, false, 'tornou_inadequada'],
            'inadequada -> inadequada' => [false, false, 'manteve_inadequada'],
        ];
    }
}
