<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Model\AguaAmostra;
use Model\Biofiltro;

$antes = new AguaAmostra(7.0, 10.0, 0.5, 200.0, 25.0);
$depois = new AguaAmostra(7.1, 1.0, 0.3, 150.0, 24.0);

echo $antes->parecer() . PHP_EOL;

foreach ((new Biofiltro($antes, $depois))->comparar() as $item) {
    echo $item['parametro'] . ': eficiência = ' . ($item['eficiencia'] ?? 'n/a') . PHP_EOL;
}
