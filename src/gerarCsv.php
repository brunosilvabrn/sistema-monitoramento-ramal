<?php

require '../class/RelatorioClass.php';

$relatorio = new RelatorioClass();
$csvContent = $relatorio->gerarRelatorio();

$content = 'Content-Disposition: attachment; filename="dados-ramais-'.date('d-m-Y-h:m'). '.csv";';
header('Content-Type: text/csv');
header($content);

echo $csvContent;