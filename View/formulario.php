<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise de Água - Biofiltro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet">
</head>
<body class="bg-light p-5">

    <div class="container bg-white p-4 rounded shadow col-md-6">
        <h3>Laboratório de Análise da Água</h3>
        <form action="index.php?action=analisar" method="POST" class="mt-3">
            <div class="mb-3">
                <label class="form-label">pH</label>
                <input type="number" step="0.1" name="ph" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Turbidez (UNT)</label>
                <input type="number" step="0.1" name="turbidez" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Cloro Residual (mg/L)</label>
                <input type="number" step="0.01" name="cloro" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Dureza (mg/L CaCO₃)</label>
                <input type="number" step="0.1" name="dureza" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Temperatura (°C)</label>
                <input type="number" step="0.1" name="temperatura" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Analisar</button>
        </form>
    </div>

</body>
</html>