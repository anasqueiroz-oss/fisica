<?php

require_once __DIR__ . '/vendor/autoload.php';

use Controller\AguaController;

$controller = new AguaController();

$action = $_GET['action'] ?? 'index';

if ($action === 'analisar') {
    $controller->analisar();
} elseif ($action === 'amostras') {
    $controller->amostras();
} else {
    $controller->index();
}
