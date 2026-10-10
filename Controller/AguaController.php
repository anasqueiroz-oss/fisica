<?php

namespace Controller;

use InvalidArgumentException;
use Model\AguaAmostra;
use Model\Biofiltro;
use Model\DatasetAmostras;

class AguaController
{
    private const CAMPOS = ['ph', 'turbidez', 'cloro', 'dureza', 'temperatura'];

    public function index(): void
    {
        $erro = null;

        require __DIR__ . '/../View/formulario.php';
    }

    public function amostras(): void
    {
        $amostras = [];
        $erro = null;

        try {
            $amostras = DatasetAmostras::carregar(__DIR__ . '/../data/amostras_reais.csv');
        } catch (InvalidArgumentException $e) {
            $erro = $e->getMessage();
        }

        require __DIR__ . '/../View/amostras.php';
    }

    public function analisar(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: index.php');
            exit;
        }

        try {
            $antes = $this->lerAmostra('', 'antes do filtro');
            $depois = $this->lerAmostra('_depois', 'depois do filtro');
        } catch (InvalidArgumentException $e) {
            http_response_code(422);
            $erro = $e->getMessage();

            require __DIR__ . '/../View/formulario.php';
            return;
        }

        $biofiltro = new Biofiltro($antes, $depois);

        $analiseAntes = $antes->analisar();
        $analiseDepois = $depois->analisar();
        $parecerAntes = $antes->parecer();
        $parecerDepois = $depois->parecer();
        $comparacao = $biofiltro->comparar();
        $situacao = $biofiltro->situacao();
        $descricaoSituacao = $biofiltro->descricaoSituacao();

        require __DIR__ . '/../View/resultado.php';
    }

    private function lerAmostra(string $sufixo, string $rotulo): AguaAmostra
    {
        $valores = [];

        foreach (self::CAMPOS as $campo) {
            $bruto = $_POST[$campo . $sufixo] ?? '';
            $bruto = is_scalar($bruto) ? str_replace(',', '.', trim((string) $bruto)) : '';

            if ($bruto === '' || filter_var($bruto, FILTER_VALIDATE_FLOAT) === false) {
                throw new InvalidArgumentException("Informe um valor numérico válido para {$campo} ({$rotulo}).");
            }

            $valores[$campo] = (float) $bruto;
        }

        return new AguaAmostra(
            $valores['ph'],
            $valores['turbidez'],
            $valores['cloro'],
            $valores['dureza'],
            $valores['temperatura']
        );
    }
}
