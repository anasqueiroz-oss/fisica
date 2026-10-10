<?php

declare(strict_types=1);

namespace Model;

use InvalidArgumentException;

class AguaAmostra
{
    public const PH_MIN = 6.0;
    public const PH_MAX = 9.5;
    public const TURBIDEZ_MAX = 5.0;   // uT
    public const CLORO_MIN = 0.2;      // mg/L (cloro residual livre)
    public const CLORO_MAX = 5.0;      // mg/L
    public const DUREZA_MAX = 300.0;   // mg/L CaCO3

    private float $ph;
    private float $turbidez;
    private float $cloro;
    private float $dureza;
    private float $temperatura;

    public function __construct(
        float $ph,
        float $turbidez,
        float $cloro,
        float $dureza,
        float $temperatura
    ) {
        self::validar('pH', $ph, 0.0, 14.0);
        self::validar('Turbidez', $turbidez, 0.0);
        self::validar('Cloro residual', $cloro, 0.0);
        self::validar('Dureza', $dureza, 0.0);
        self::validar('Temperatura', $temperatura, 0.0, 100.0);

        $this->ph = $ph;
        $this->turbidez = $turbidez;
        $this->cloro = $cloro;
        $this->dureza = $dureza;
        $this->temperatura = $temperatura;
    }

    public function getPh(): float
    {
        return $this->ph;
    }

    public function getTurbidez(): float
    {
        return $this->turbidez;
    }

    public function getCloro(): float
    {
        return $this->cloro;
    }

    public function getDureza(): float
    {
        return $this->dureza;
    }

    public function getTemperatura(): float
    {
        return $this->temperatura;
    }

    /**
     * Classifica cada parâmetro. Cada item contém:
     * chave, nome, valor, unidade, referencia, situacao
     * ('adequado' | 'abaixo' | 'acima' | 'informativo') e adequado (bool|null).
     *
     * @return array<int, array<string, mixed>>
     */
    public function analisar(): array
    {
        return [
            $this->avaliar('ph', 'pH', $this->ph, '', self::PH_MIN, self::PH_MAX, '6,0 a 9,5'),
            $this->avaliar('turbidez', 'Turbidez', $this->turbidez, 'uT', null, self::TURBIDEZ_MAX, 'máximo de 5 uT'),
            $this->avaliar('cloro', 'Cloro residual livre', $this->cloro, 'mg/L', self::CLORO_MIN, self::CLORO_MAX, '0,2 a 5,0 mg/L'),
            $this->avaliar('dureza', 'Dureza total', $this->dureza, 'mg/L CaCO3', null, self::DUREZA_MAX, 'máximo de 300 mg/L CaCO3'),
            [
                'chave' => 'temperatura',
                'nome' => 'Temperatura',
                'valor' => $this->temperatura,
                'unidade' => '°C',
                'referencia' => 'sem limite definido (apenas monitorada)',
                'situacao' => 'informativo',
                'adequado' => null,
            ],
        ];
    }

    /** A amostra é adequada quando todos os parâmetros com faixa estão dentro dela. */
    public function estaAdequada(): bool
    {
        return $this->parametrosInadequados() === [];
    }

    /** @return string[] nomes dos parâmetros fora da faixa */
    public function parametrosInadequados(): array
    {
        $fora = [];
        foreach ($this->analisar() as $item) {
            if ($item['adequado'] === false) {
                $fora[] = $item['nome'];
            }
        }

        return $fora;
    }

    public function statusPotabilidade(): string
    {
        return $this->estaAdequada() ? 'Adequada' : 'Inadequada';
    }

    public function parecer(): string
    {
        if ($this->estaAdequada()) {
            return 'Adequada: todos os parâmetros avaliados estão dentro das faixas '
                . 'da Portaria GM/MS nº 888/2021.';
        }

        return 'Inadequada: fora da faixa de referência - '
            . implode(', ', $this->parametrosInadequados()) . '.';
    }

    private function avaliar(
        string $chave,
        string $nome,
        float $valor,
        string $unidade,
        ?float $min,
        ?float $max,
        string $referencia
    ): array {
        if ($min !== null && $valor < $min) {
            $situacao = 'abaixo';
        } elseif ($max !== null && $valor > $max) {
            $situacao = 'acima';
        } else {
            $situacao = 'adequado';
        }

        return [
            'chave' => $chave,
            'nome' => $nome,
            'valor' => $valor,
            'unidade' => $unidade,
            'referencia' => $referencia,
            'situacao' => $situacao,
            'adequado' => $situacao === 'adequado',
        ];
    }

    private static function validar(string $nome, float $valor, float $min, ?float $max = null): void
    {
        if (!is_finite($valor)) {
            throw new InvalidArgumentException("{$nome}: valor numérico inválido.");
        }
        if ($valor < $min || ($max !== null && $valor > $max)) {
            $intervalo = $max === null ? "maior ou igual a {$min}" : "entre {$min} e {$max}";
            throw new InvalidArgumentException("{$nome}: o valor deve estar {$intervalo}.");
        }
    }
}
