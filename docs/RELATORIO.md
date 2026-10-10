RELATÓRIO TECNICO - Laboratório Digital de Qualidade da Água
alunos: Ana Sandrine e Eduardo Rodrigues

1. OBJETIVO:

Desenvolver uma aplicação web em PHP (arquitetura MVC) que classifica amostras de água de acordo com o padrão de potabilidade brasileiro e avalia o desempenho de um biofiltro comparando os parâmetros antes e depois da filtragem.

2. LÓGICA DOS ALGORITMOS:

**Validação.** Cada amostra (`AguaAmostra`) recebe pH, turbidez, cloro residual livre, dureza total e temperatura. Valores não numéricos ou fora do intervalo físico (pH fora de 0-14, temperatura fora de 0-100 °C, demais negativos) lançam `InvalidArgumentException`.

**Classificação.** Cada parâmetro é comparado com sua faixa de referência (limites inclusivos) e recebe uma situação: `adequado`, `abaixo` ou `acima`. A temperatura não tem padrão de potabilidade e é só informativa. A amostra é **adequada** quando todos os parâmetros com faixa estão dentro dela; o parecer lista os parâmetros que ficaram fora.

**Situação do biofiltro.** Compara a adequação antes e depois e resulta em uma de quatro situações: manteve adequada, tornou adequada, tornou inadequada ou manteve inadequada.

3. FAIXAS DE REFERENCIA:

Fonte: Brasil. Ministério da Saúde. Portaria GM/MS nº 888, de 4 de maio de 2021 (altera o Anexo XX da Portaria de Consolidação GM/MS nº 5/2017).

| Parâmetro | Faixa |
|---|---|
| pH | 6,0 a 9,5 (recomendado) |
| Turbidez | máx. 5 uT |
| Cloro residual livre | 0,2 a 5,0 mg/L |
| Dureza total | máx. 300 mg/L CaCO₃ |
| Temperatura | sem limite (informativa) |

[PREENCHER: citar o artigo/anexo exato da Portaria depois de conferir o texto oficial.]

4. MÉTODO DO BIOFILTRO:

A eficiência de remoção de cada parâmetro é calculada por

    E (%) = (C_antes − C_depois) / C_antes × 100

e é aplicada a turbidez, cloro residual e dureza. Valor positivo indica redução; negativo indica aumento após o filtro. Se C_antes = 0 a eficiência é indefinida e o sistema retorna "não aplicável", evitando divisão por zero. Para pH e temperatura, a "remoção" não tem sentido físico e o sistema mostra somente a variação (depois − antes). Observação: uma redução do cloro residual aparece como "remoção", mas não é necessariamente desejável, pois o cloro residual precisa permanecer acima de 0,2 mg/L.

5. RELAÇÃO COM QUÍMICA, BIOLOGIA E ODS 6: 

**Química:** pH e dureza descrevem a química da água (acidez/basicidade e íons de cálcio e magnésio); o cloro residual é um desinfetante que precisa permanecer na água.
**Biologia:** o cloro residual protege contra microrganismos patogênicos, e a turbidez alta pode abrigá-los, reduzindo a eficácia da desinfecção; o biofiltro atua por retenção e ação biológica nas camadas.
**ODS 6 (Água potável e saneamento):** o sistema se relaciona com a meta 6.1 (acesso a água potável segura) e a meta 6.3 (melhoria da qualidade da água), ao verificar se a água atende ao padrão de potabilidade e se o filtro melhora seus indicadores.

REFERENCIAS:

BRASIL. Ministério da Saúde. Portaria GM/MS nº 888, de 4 de maio de 2021. Altera o Anexo XX da Portaria de Consolidação GM/MS nº 5, de 28 de setembro de 2017, para dispor sobre os procedimentos de controle e de vigilância da qualidade da água para consumo humano e seu padrão de potabilidade.
