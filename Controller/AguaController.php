<?php
require_once __DIR__ . '/../models/AguaModel.php';


class AguaController {
    
    // formulário
    public function index(): void {
        require_once __DIR__ . '/../views/formulario.php';
    }


    public function analisar(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new AguaModel();
            
            $resultado = $model->processarAnalise($_POST);
            
            $salvo = $model->salvarNoSupabase($resultado);
            
            require_once __DIR__ . '/../views/resultado.php';
        } else {
            header('Location: index.php');
        }
    }
}