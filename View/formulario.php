
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise da Qualidade da Água</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

    <main class="container py-5" style="max-width: 650px;">
        <h2 class="mb-2">Análise da Qualidade da Água</h2>
        <p class="text-secondary mb-4">
            Informe os valores da amostra para realizar a análise.
        </p>

        <form action="index.php?action=analisar" method="POST">

            <div class="mb-3">
                <label for="ph" class="form-label">pH</label>
                <input
                    type="number"
                    name="ph"
                    id="ph"
                    class="form-control"
                    min="0"
                    max="14"
                    step="0.1"
                    placeholder="Ex.: 6.5"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="turbidez" class="form-label">
                    Turbidez (UNT)
                </label>
                <input
                    type="number"
                    name="turbidez"
                    id="turbidez"
                    class="form-control"
                    min="0"
                    step="0.1"
                    placeholder="Ex.: 12.0"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="cloro" class="form-label">
                    Cloro residual (mg/L)
                </label>
                <input
                    type="number"
                    name="cloro"
                    id="cloro"
                    class="form-control"
                    min="0"
                    step="0.01"
                    placeholder="Ex.: 0.2"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="dureza" class="form-label">
                    Dureza (mg/L CaCO₃)
                </label>
                <input
                    type="number"
                    name="dureza"
                    id="dureza"
                    class="form-control"
                    min="0"
                    step="0.1"
                    placeholder="Ex.: 150"
                    required
                >
            </div>

            <div class="mb-4">
                <label for="temperatura" class="form-label">
                    Temperatura (°C)
                </label>
                <input
                    type="number"
                    name="temperatura"
                    id="temperatura"
                    class="form-control"
                    step="0.1"
                    placeholder="Ex.: 25"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Analisar amostra
            </button>

        </form>
    </main>

</body>
</html>
