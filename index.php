
<?php

require_once __DIR__ . '/Controller/AguaController.php';

$controller = new AguaController();

$action = $_GET['action'] ?? 'index';

if ($action === 'analisar') {
    $controller->analisar();
} else {
    $controller->index();
}
