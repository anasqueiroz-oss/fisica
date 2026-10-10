<?php

declare(strict_types=1);

namespace Model;

use InvalidArgumentException;

class DatasetAmostras
{
    private const COLUNAS = [
        'id_amostra', 'data_coleta', 'local_origem', 'etapa',
        'ph', 'turbidez_uT', 'cloro_mg_L', 'dureza_mg_L_CaCO3', 'temperatura_C',
    ];

    /**
     * @return array<int, array{id: string, data: string, local: string, biofiltro: Biofiltro}>
     */
    public static function carregar(string $caminho): array
    {
        $linhas = is_file($caminho) && is_readable($caminho)
            ? file($caminho, FILE_IGNORE_NEW_LINES)
            : false;

        if ($linhas === false) {
            throw new InvalidArgumentException("Arquivo de dados não encontrado: {$caminho}");
        }

        $cabecalho = null;
        $grupos = [];

        foreach ($linhas as $indice => $linha) {
            $numero = $indice + 1;
            $linha = rtrim((string) preg_replace('/^\xEF\xBB\xBF/', '', $linha));

            if ($linha === '') {
                continue;
            }

            $colunas = array_map('trim', str_getcsv($linha, ',', '"', ''));

            if ($cabecalho === null) {
                foreach (self::COLUNAS as $coluna) {
                    if (!in_array($coluna, $colunas, true)) {
                        throw new InvalidArgumentException("Coluna ausente no cabeçalho: {$coluna}.");
                    }
                }
                $cabecalho = $colunas;
                continue;
            }

            if (count($colunas) !== count($cabecalho)) {
                throw new InvalidArgumentException("Linha {$numero}: número de colunas incorreto.");
            }

            $registro = array_combine($cabecalho, $colunas);

            foreach (self::COLUNAS as $coluna) {
                if ($registro[$coluna] === '') {
                    throw new InvalidArgumentException("Linha {$numero}: campo ausente ({$coluna}).");
                }
            }

            $etapa = strtolower($registro['etapa']);
            if (!in_array($etapa, ['antes', 'depois'], true)) {
                throw new InvalidArgumentException("Linha {$numero}: a etapa deve ser 'antes' ou 'depois'.");
            }

            $id = $registro['id_amostra'];
            if (isset($grupos[$id][$etapa])) {
                throw new InvalidArgumentException("Linha {$numero}: etapa '{$etapa}' repetida para a amostra {$id}.");
            }

            $grupos[$id][$etapa] = [
                'data' => $registro['data_coleta'],
                'local' => $registro['local_origem'],
                'amostra' => self::criarAmostra($registro, $numero),
            ];
        }

        $resultado = [];
        foreach ($grupos as $id => $etapas) {
            if (!isset($etapas['antes'], $etapas['depois'])) {
                throw new InvalidArgumentException("Amostra {$id}: faltam as etapas 'antes' e 'depois'.");
            }

            $resultado[] = [
                'id' => (string) $id,
                'data' => $etapas['antes']['data'],
                'local' => $etapas['antes']['local'],
                'biofiltro' => new Biofiltro($etapas['antes']['amostra'], $etapas['depois']['amostra']),
            ];
        }

        return $resultado;
    }

    private static function criarAmostra(array $registro, int $numero): AguaAmostra
    {
        try {
            return new AguaAmostra(
                self::numero($registro, 'ph', $numero),
                self::numero($registro, 'turbidez_uT', $numero),
                self::numero($registro, 'cloro_mg_L', $numero),
                self::numero($registro, 'dureza_mg_L_CaCO3', $numero),
                self::numero($registro, 'temperatura_C', $numero)
            );
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException("Linha {$numero}: " . $e->getMessage(), 0, $e);
        }
    }

    private static function numero(array $registro, string $campo, int $numero): float
    {
        $valor = str_replace(',', '.', $registro[$campo]);

        if (!is_numeric($valor)) {
            throw new InvalidArgumentException("Linha {$numero}: valor numérico inválido em {$campo}.");
        }

        return (float) $valor;
    }
}
