<?php
$e = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

$campos = [
    'ph'          => ['label' => 'pH',                         'step' => '0.1',  'min' => '0', 'max' => '14', 'ex' => '6.5'],
    'turbidez'    => ['label' => 'Turbidez (uT)',              'step' => '0.1',  'min' => '0', 'max' => null, 'ex' => '12.0'],
    'cloro'       => ['label' => 'Cloro residual livre (mg/L)', 'step' => '0.01', 'min' => '0', 'max' => null, 'ex' => '0.2'],
    'dureza'      => ['label' => 'Dureza total (mg/L CaCO₃)',  'step' => '0.1',  'min' => '0', 'max' => null, 'ex' => '150'],
    'temperatura' => ['label' => 'Temperatura (°C)',           'step' => '0.1',  'min' => '0', 'max' => '100', 'ex' => '25'],
];

$blocos = [
    ''        => 'Antes do biofiltro',
    '_depois' => 'Depois do biofiltro',
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise da Qualidade da Água</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <main class="container py-5" style="max-width: 760px;">
        <h2 class="mb-2">Análise da Qualidade da Água</h2>
        <p class="text-secondary mb-4">
            Informe os valores da amostra antes e depois do biofiltro. Use ponto ou vírgula como separador decimal.
        </p>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?= $e($erro) ?></div>
        <?php endif; ?>

        <form action="index.php?action=analisar" method="POST">
            <div class="row">
                <?php foreach ($blocos as $sufixo => $titulo): ?>
                    <div class="col-md-6">
                        <h5 class="mb-3"><?= $e($titulo) ?></h5>

                        <?php foreach ($campos as $nome => $c): ?>
                            <?php $id = $nome . $sufixo; ?>
                            <div class="mb-3">
                                <label for="<?= $e($id) ?>" class="form-label"><?= $e($c['label']) ?></label>
                                <input
                                    type="number"
                                    name="<?= $e($id) ?>"
                                    id="<?= $e($id) ?>"
                                    class="form-control"
                                    min="<?= $e($c['min']) ?>"
                                    <?php if ($c['max'] !== null): ?>max="<?= $e($c['max']) ?>"<?php endif; ?>
                                    step="<?= $e($c['step']) ?>"
                                    placeholder="Ex.: <?= $e($c['ex']) ?>"
                                    value="<?= $e($_POST[$id] ?? '') ?>"
                                    required
                                >
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2">Analisar amostra</button>
        </form>

        <a href="index.php?action=amostras" class="d-block text-center mt-3">Ver amostras reais cadastradas</a>
    </main>

</body>
</html>
