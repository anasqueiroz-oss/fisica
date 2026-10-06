<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratório de Análise de Água - Biofiltro</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Laboratório Digital da Água</h3>
                        <small>Simulação de Potabilidade e Biofiltro</small>
                    </div>
                    <div class="card-body">
                    
                        <form action="processar.php" method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="ph" class="form-label">pH da Água</label>
                                    <input type="number" step="0.1" min="0" max="14" class="form-control" id="ph" name="ph" placeholder="Ex: 6.5" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="turbidez" class="form-label">Turbidez (UNT)</label>
                                    <input type="number" step="0.1" min="0" class="form-control" id="turbidez" name="turbidez" placeholder="Ex: 12.0" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="cloro" class="form-label">Cloro Residual (mg/L)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cloro" name="cloro" placeholder="Ex: 0.2" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="dureza" class="form-label">Dureza (mg/L CaCO₃)</label>
                                    <input type="number" step="0.1" min="0" class="form-control" id="dureza" name="dureza" placeholder="Ex: 150.0" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="temperatura" class="form-label">Temperatura (°C)</label>
                                <input type="number" step="0.1" class="form-control" id="temperatura" name="temperatura" placeholder="Ex: 25.0" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">Analisar Água e Simular Biofiltro</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
