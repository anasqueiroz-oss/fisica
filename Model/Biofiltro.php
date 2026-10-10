<?php

declare(strict_types=1);

namespace Model;

use InvalidArgumentException;

class Biofiltro
{
    private const PARAMETROS_REMOCAO = ['turbidez', 'cloro', 'dureza'];

    public function __construct(
        private AguaAmostra $antes,
        private AguaAmostra $depois
    ) {
    }

    public function getAntes(): AguaAmostra
    {
        return $this->antes;
    }

    public function getDepois(): AguaAmostra
    {
        return $this->depois;
    }

    public static function calcularEficiencia(float $antes, float $depois): ?float
    {
        if (!is_finite($antes) || !is_finite($depois)) {
            throw new InvalidArgumentException('Os valores devem ser numéricos finitos.');
        }
        if ($antes < 0.0 || $depois < 0.0) {
            throw new InvalidArgumentException('Os valores não podem ser negativos.');
        }
        if ($antes === 0.0) {
            return null;
        }

        return round((($antes - $depois) / $antes) * 100, 2);
    }

    /**
     * @return array<int, array<string, mixed>> chave, parametro, unidade, antes,
     *         depois, variacao (depois - antes), eficiencia (%|null), removivel (bool)
     */
    public function comparar(): array
    {
        $pares = [
            'ph' => ['pH', '', $this->antes->getPh(), $this->depois->getPh()],
            'turbidez' => ['Turbidez', 'uT', $this->antes->getTurbidez(), $this->depois->getTurbidez()],
            'cloro' => ['Cloro residual livre', 'mg/L', $this->antes->getCloro(), $this->depois->getCloro()],
            'dureza' => ['Dureza total', 'mg/L CaCO3', $this->antes->getDureza(), $this->depois->getDureza()],
            'temperatura' => ['Temperatura', '°C', $this->antes->getTemperatura(), $this->depois->getTemperatura()],
        ];

        $itens = [];
        foreach ($pares as $chave => [$nome, $unidade, $antes, $depois]) {
            $removivel = in_array($chave, self::PARAMETROS_REMOCAO, true);
            $itens[] = [
                'chave' => $chave,
                'parametro' => $nome,
                'unidade' => $unidade,
                'antes' => $antes,
                'depois' => $depois,
                'variacao' => round($depois - $antes, 2),
                'eficiencia' => $removivel ? self::calcularEficiencia($antes, $depois) : null,
                'removivel' => $removivel,
            ];
        }

        return $itens;
    }

    /* manteve_adequada | tornou_adequada | tornou_inadequada | manteve_inadequada */
    public function situacao(): string
    {
        $antes = $this->antes->estaAdequada();
        $depois = $this->depois->estaAdequada();

        return match (true) {
            $antes && $depois => 'manteve_adequada',
            !$antes && $depois => 'tornou_adequada',
            $antes && !$depois => 'tornou_inadequada',
            default => 'manteve_inadequada',
        };
    }

    public function descricaoSituacao(): string
    {
        return match ($this->situacao()) {
            'manteve_adequada' => 'A água já era adequada e continuou adequada após o biofiltro.',
            'tornou_adequada' => 'O biofiltro tornou a água adequada às faixas de referência.',
            'tornou_inadequada' => 'Atenção: a água era adequada e ficou inadequada após o biofiltro.',
            default => 'A água continuou inadequada após o biofiltro.',
        };
    }
}
