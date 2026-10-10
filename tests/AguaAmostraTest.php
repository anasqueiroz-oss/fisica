<?php

declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use Model\AguaAmostra;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(AguaAmostra::class)]
final class AguaAmostraTest extends TestCase
{
    private function criar(array $valores = []): AguaAmostra
    {
        $v = array_merge([
            'ph' => 7.0,
            'turbidez' => 1.0,
            'cloro' => 0.5,
            'dureza' => 100.0,
            'temperatura' => 25.0,
        ], $valores);

        return new AguaAmostra($v['ph'], $v['turbidez'], $v['cloro'], $v['dureza'], $v['temperatura']);
    }

    private function situacao(AguaAmostra $amostra, string $chave): string
    {
        foreach ($amostra->analisar() as $item) {
            if ($item['chave'] === $chave) {
                return $item['situacao'];
            }
        }

        $this->fail("Parâmetro {$chave} não encontrado na análise.");
    }

    public function testGettersRetornamOsValoresInformados(): void
    {
        $amostra = new AguaAmostra(7.2, 1.5, 0.8, 120.0, 22.5);

        $this->assertSame(7.2, $amostra->getPh());
        $this->assertSame(1.5, $amostra->getTurbidez());
        $this->assertSame(0.8, $amostra->getCloro());
        $this->assertSame(120.0, $amostra->getDureza());
        $this->assertSame(22.5, $amostra->getTemperatura());
    }

    public function testAmostraDentroDasFaixasEhAdequada(): void
    {
        $amostra = $this->criar();

        $this->assertTrue($amostra->estaAdequada());
        $this->assertSame('Adequada', $amostra->statusPotabilidade());
        $this->assertSame([], $amostra->parametrosInadequados());
        $this->assertStringContainsString('Portaria GM/MS nº 888/2021', $amostra->parecer());
    }

    public function testAnalisarRetornaOsCincoParametros(): void
    {
        $itens = $this->criar()->analisar();

        $this->assertCount(5, $itens);
        $this->assertSame(
            ['ph', 'turbidez', 'cloro', 'dureza', 'temperatura'],
            array_column($itens, 'chave')
        );
    }

    #[DataProvider('limitesProvider')]
    public function testClassificacaoNosLimitesDasFaixas(string $chave, float $valor, string $esperado): void
    {
        $amostra = $this->criar([$chave => $valor]);

        $this->assertSame($esperado, $this->situacao($amostra, $chave));
    }

    public static function limitesProvider(): array
    {
        return [
            'pH no minimo' => ['ph', 6.0, 'adequado'],
            'pH abaixo do minimo' => ['ph', 5.99, 'abaixo'],
            'pH no maximo' => ['ph', 9.5, 'adequado'],
            'pH acima do maximo' => ['ph', 9.51, 'acima'],
            'turbidez zero' => ['turbidez', 0.0, 'adequado'],
            'turbidez no maximo' => ['turbidez', 5.0, 'adequado'],
            'turbidez acima do maximo' => ['turbidez', 5.01, 'acima'],
            'cloro zero' => ['cloro', 0.0, 'abaixo'],
            'cloro abaixo do minimo' => ['cloro', 0.19, 'abaixo'],
            'cloro no minimo' => ['cloro', 0.2, 'adequado'],
            'cloro no maximo' => ['cloro', 5.0, 'adequado'],
            'cloro acima do maximo' => ['cloro', 5.01, 'acima'],
            'dureza no maximo' => ['dureza', 300.0, 'adequado'],
            'dureza acima do maximo' => ['dureza', 300.01, 'acima'],
            'temperatura e informativa' => ['temperatura', 25.0, 'informativo'],
        ];
    }

    public function testTemperaturaNuncaReprovaAAmostra(): void
    {
        $amostra = $this->criar(['temperatura' => 99.0]);

        $this->assertTrue($amostra->estaAdequada());
        $this->assertNull($amostra->analisar()[4]['adequado']);
    }

    public function testParecerListaOsParametrosForaDaFaixa(): void
    {
        $amostra = $this->criar(['ph' => 5.0, 'turbidez' => 6.0]);

        $this->assertFalse($amostra->estaAdequada());
        $this->assertSame('Inadequada', $amostra->statusPotabilidade());
        $this->assertSame(['pH', 'Turbidez'], $amostra->parametrosInadequados());
        $this->assertStringContainsString('pH, Turbidez', $amostra->parecer());
    }

    #[DataProvider('valoresInvalidosProvider')]
    public function testValoresInvalidosLancamExcecao(string $chave, float $valor): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->criar([$chave => $valor]);
    }

    public static function valoresInvalidosProvider(): array
    {
        return [
            'pH negativo' => ['ph', -0.1],
            'pH acima de 14' => ['ph', 14.1],
            'pH NaN' => ['ph', NAN],
            'pH infinito' => ['ph', INF],
            'turbidez negativa' => ['turbidez', -1.0],
            'cloro negativo' => ['cloro', -0.1],
            'dureza negativa' => ['dureza', -1.0],
            'temperatura negativa' => ['temperatura', -0.1],
            'temperatura acima de 100' => ['temperatura', 100.1],
            'temperatura NaN' => ['temperatura', NAN],
        ];
    }
}
