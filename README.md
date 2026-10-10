QUALIDADE DA ÁGUA:

Laboratório digital em PHP (MVC) que classifica uma amostra de água pelas faixas da Portaria GM/MS nº 888/2021 e calcula a eficiência de um biofiltro comparando os valores antes e depois.

ESTRUTURA:

Controller/AguaController.php   recebe o formulário e chama os models
Model/AguaAmostra.php           validação e classificação dos 5 parâmetros
Model/Biofiltro.php             comparação antes/depois e eficiência de remoção
Model/DatasetAmostras.php       leitura e validação do dataset real (CSV)
View/formulario.php             entrada dos dados 
View/resultado.php              análise completa e comparação
View/amostras.php               amostras reais cadastradas
tests/                          testes PHPUnit
data/amostras_reais.csv         dataset real de medições 
docs/RELATORIO.md               relatório técnico


INSTALAÇÃO:

Requisitos: PHP 8.4+ e Composer.

composer install


EXECUÇÃO HERD:

1. Abrir o link do site dentro do herd.
2. Preencher os valores antes e depois do biofiltro e clicar em "Analisar amostra".

ALGORITMOS:

- Classificação (`AguaAmostra::analisar`): compara cada parâmetro com a faixa (limites inclusivos). pH 6,0–9,5; turbidez ≤ 5 uT; cloro 0,2–5,0 mg/L; dureza ≤ 300 mg/L CaCO₃. A temperatura é apenas informativa. Fonte: Portaria GM/MS nº 888/2021 (detalhes no relatório).

- Dataset (`DatasetAmostras::carregar`): lê o CSV, exige uma linha "antes" e uma "depois" por amostra e rejeita campos ausentes, números inválidos e valores impossíveis.
- Validação: pH entre 0 e 14, temperatura entre 0 e 100 °C, demais valores ≥ 0; valores não numéricos lançam "InvalidArgumentException".

- Eficiência do biofiltro (`Biofiltro::calcularEficiencia`): "(antes − depois) / antes × 100". Aplicada a turbidez, cloro e dureza. Se o valor inicial é 0, retorna "null" (sem divisão por zero). Negativo = aumento. Para pH e temperatura mostra só a variação.

TESTES E COBERTURA:

vendor/bin/phpunit

Cobertura (exige Xdebug ou PCOV):

XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text

DADOS REAIS:

Preencha "data/amostras_reais.csv" com uma linha "antes" e uma "depois" por "id_amostra" (somente medições reais). Depois abra "http://fisica.test/index.php?action=amostras" para ver a classificação e a eficiência de cada amostra.
