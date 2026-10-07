<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado MVC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet">
</head>
<body class="bg-light p-5">


    <div class="container bg-white p-4 rounded shadow col-md-6 text-center">
        <h2>Resultado da Filtragem</h2>
        <hr>
        <p><strong>pH Filtrado:</strong> <?= $resultado['ph_filtrado'] ?></p>
        <p><strong>Turbidez Filtrada:</strong> <?= $resultado['turbidez_filtrada'] ?> UNT</p>
        
        <div class="alert <?= $resultado['status_potabilidade'] === 'Inadequada' ? 'alert-danger' : 'alert-success' ?>">
            Status: <?= $resultado['status_potabilidade'] ?>
        </div>


        <?php if ($salvo): ?>
            <p class="text-success"><small>Guardado no Supabase com sucesso!</small></p>
        <?php else: ?>
            <p class="text-danger"><small>Erro ao guardar no Supabase.</small></p>
        <?php endif; ?>


        <a href="index.php" class="btn btn-secondary mt-3">Voltar</a>
    </div>
    
</body>
</html>