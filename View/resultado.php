<?php
$e = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$n = static fn(?float $v, int $casas = 2): string => $v === null ? '—' : number_format($v, $casas, ',', '.');

$rotulos = [
    'adequado'    => ['Adequado', 'success'],
    'abaixo'      => ['Abaixo do mínimo', 'danger'],
    'acima'       => ['Acima do máximo', 'danger'],
    'informativo' => ['Informativo', 'secondary'],
];

$classeSituacao = match ($situacao) {
    'manteve_adequada', 'tornou_adequada' => 'alert-success',
    'tornou_inadequada'                   => 'alert-danger',
    default                               => 'alert-warning',
};

$tabelas = [
    'Antes do biofiltro'  => [$analiseAntes, $parecerAntes],
    'Depois do biofiltro' => [$analiseDepois, $parecerDepois],
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado da Análise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <main class="container py-5" style="max-width: 900px;">
        <h2 class="mb-4">Resultado da Análise</h2>

        <div class="alert <?= $e($classeSituacao) ?>"><?= $e($descricaoSituacao) ?></div>

        <?php foreach ($tabelas as $titulo => [$analise, $parecer]): ?>
            <div class="bg-white p-4 rounded shadow-sm mb-4">
                <h5><?= $e($titulo) ?></h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Parâmetro</th>
                                <th>Valor</th>
                                <th>Referência</th>
                                <th>Situação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($analise as $item): ?>
                                <?php [$texto, $cor] = $rotulos[$item['situacao']]; ?>
                                <tr>
                                    <td><?= $e($item['nome']) ?></td>
                                    <td><?= $n($item['valor']) ?> <?= $e($item['unidade']) ?></td>
                                    <td><small class="text-secondary"><?= $e($item['referencia']) ?></small></td>
                                    <td><span class="badge text-bg-<?= $e($cor) ?>"><?= $e($texto) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="mb-0"><strong>Parecer:</strong> <?= $e($parecer) ?></p>
            </div>
        <?php endforeach; ?>

        <div class="bg-white p-4 rounded shadow-sm mb-4">
            <h5>Comparação antes e depois</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Parâmetro</th>
                            <th>Antes</th>
                            <th>Depois</th>
                            <th>Variação</th>
                            <th>Eficiência de remoção</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comparacao as $item): ?>
                            <tr>
                                <td><?= $e($item['parametro']) ?></td>
                                <td><?= $n($item['antes']) ?> <?= $e($item['unidade']) ?></td>
                                <td><?= $n($item['depois']) ?> <?= $e($item['unidade']) ?></td>
                                <td><?= ($item['variacao'] > 0 ? '+' : '') . $n($item['variacao']) ?></td>
                                <td>
                                    <?php if (!$item['removivel']): ?>
                                        <small class="text-secondary">não se aplica</small>
                                    <?php elseif ($item['eficiencia'] === null): ?>
                                        <small class="text-secondary">indefinida (valor inicial zero)</small>
                                    <?php else: ?>
                                        <?= $n($item['eficiencia']) ?>%
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <small class="text-secondary">
                Eficiência = (antes − depois) ÷ antes × 100. Valor negativo indica aumento do parâmetro após o filtro.
            </small>
        </div>

        <a href="index.php" class="btn btn-secondary">Nova análise</a>
    </main>

</body>
</html>
