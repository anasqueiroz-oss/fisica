<?php
$e = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$n = static fn(?float $v): string => $v === null ? '—' : number_format($v, 2, ',', '.') . '%';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amostras Reais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <main class="container py-5">
        <h2 class="mb-4">Amostras reais cadastradas</h2>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?= $e($erro) ?></div>
        <?php elseif ($amostras === []): ?>
            <div class="alert alert-info">Nenhuma amostra cadastrada. Preencha o arquivo data/amostras_reais.csv.</div>
        <?php else: ?>
            <div class="table-responsive bg-white p-3 rounded shadow-sm">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Amostra</th>
                            <th>Data</th>
                            <th>Local</th>
                            <th>Antes</th>
                            <th>Depois</th>
                            <th>Remoção turbidez</th>
                            <th>Remoção cloro</th>
                            <th>Remoção dureza</th>
                            <th>Situação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($amostras as $a): ?>
                            <?php
                            $b = $a['biofiltro'];
                            $ef = [];
                            foreach ($b->comparar() as $item) {
                                $ef[$item['chave']] = $item['eficiencia'];
                            }
                            ?>
                            <tr>
                                <td><?= $e($a['id']) ?></td>
                                <td><?= $e($a['data']) ?></td>
                                <td><?= $e($a['local']) ?></td>
                                <td><?= $e($b->getAntes()->statusPotabilidade()) ?></td>
                                <td><?= $e($b->getDepois()->statusPotabilidade()) ?></td>
                                <td><?= $n($ef['turbidez']) ?></td>
                                <td><?= $n($ef['cloro']) ?></td>
                                <td><?= $n($ef['dureza']) ?></td>
                                <td><?= $e($b->descricaoSituacao()) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn btn-secondary mt-4">Voltar</a>
    </main>

</body>
</html>
