<?php

require_once '../lib/Connect.php';
require_once '../class/LogsClass.php';

$connect = new Connect();
$logs    = new LogsClass($connect->conn());

$content = 'Content-Disposition: attachment; filename="logs-'.date('d-m-Y-h:m'). '.csv";';
header('Content-Type: text/csv');
header($content);

echo $logs->gerarRelatorio();