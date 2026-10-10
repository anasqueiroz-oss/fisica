<?php

declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use Model\DatasetAmostras;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DatasetAmostras::class)]
final class DatasetAmostrasTest extends TestCase
{
    private const CABECALHO = 'id_amostra,data_coleta,local_origem,etapa,ph,turbidez_uT,cloro_mg_L,dureza_mg_L_CaCO3,temperatura_C';
    private const ANTES = 'T1,2026-01-01,Local,antes,7.0,10.0,1.0,200.0,25.0';
    private const DEPOIS = 'T1,2026-01-01,Local,depois,7.2,2.0,0.5,150.0,24.0';

    /** @var string[] */
    private array $temporarios = [];

    protected function tearDown(): void
    {
        foreach ($this->temporarios as $arquivo) {
            @unlink($arquivo);
        }
    }

    private function arquivo(array $linhas, string $quebra = "\n"): string
    {
        $caminho = tempnam(sys_get_temp_dir(), 'amostra');
        file_put_contents($caminho, implode($quebra, $linhas) . $quebra);
        $this->temporarios[] = $caminho;

        return $caminho;
    }

    public function testCarregaParAntesDepoisEMontaOBiofiltro(): void
    {
        $amostras = DatasetAmostras::carregar($this->arquivo([self::CABECALHO, self::ANTES, self::DEPOIS]));

        $this->assertCount(1, $amostras);
        $this->assertSame('T1', $amostras[0]['id']);
        $this->assertSame('2026-01-01', $amostras[0]['data']);
        $this->assertSame('Local', $amostras[0]['local']);
        $this->assertSame(10.0, $amostras[0]['biofiltro']->getAntes()->getTurbidez());
        $this->assertSame(2.0, $amostras[0]['biofiltro']->getDepois()->getTurbidez());
    }

    public function testAceitaVirgulaDecimalLinhasEmBrancoBomECrlf(): void
    {
        $arquivo = $this->arquivo([
            "\xEF\xBB\xBF" . self::CABECALHO,
            '',
            'T2,2026-01-01,Local,Antes,"7,0","10,5",1.0,200.0,25.0',
            'T2,2026-01-01,Local,depois,7.2,2.0,0.5,150.0,24.0',
        ], "\r\n");

        $amostras = DatasetAmostras::carregar($arquivo);

        $this->assertSame(10.5, $amostras[0]['biofiltro']->getAntes()->getTurbidez());
    }

    public function testArquivoSoComCabecalhoRetornaListaVazia(): void
    {
        $this->assertSame([], DatasetAmostras::carregar($this->arquivo([self::CABECALHO])));
    }

    public function testArquivoInexistenteLancaExcecao(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DatasetAmostras::carregar(sys_get_temp_dir() . '/nao-existe-' . uniqid() . '.csv');
    }

    #[DataProvider('arquivosInvalidosProvider')]
    public function testDatasetInvalidoLancaExcecao(array $linhas): void
    {
        $this->expectException(InvalidArgumentException::class);

        DatasetAmostras::carregar($this->arquivo($linhas));
    }

    public static function arquivosInvalidosProvider(): array
    {
        return [
            'coluna ausente no cabecalho' => [['id_amostra,data_coleta,etapa', 'T1,2026-01-01,antes']],
            'numero de colunas incorreto' => [[self::CABECALHO, 'T1,2026-01-01,Local,antes,7.0']],
            'campo ausente' => [[self::CABECALHO, 'T1,2026-01-01,Local,antes,,10.0,1.0,200.0,25.0', self::DEPOIS]],
            'etapa invalida' => [[self::CABECALHO, 'T1,2026-01-01,Local,durante,7.0,10.0,1.0,200.0,25.0']],
            'etapa repetida' => [[self::CABECALHO, self::ANTES, self::ANTES]],
            'falta etapa depois' => [[self::CABECALHO, self::ANTES]],
            'numero invalido' => [[self::CABECALHO, 'T1,2026-01-01,Local,antes,abc,10.0,1.0,200.0,25.0', self::DEPOIS]],
            'valor fisicamente impossivel' => [[self::CABECALHO, 'T1,2026-01-01,Local,antes,15.0,10.0,1.0,200.0,25.0', self::DEPOIS]],
        ];
    }
}
