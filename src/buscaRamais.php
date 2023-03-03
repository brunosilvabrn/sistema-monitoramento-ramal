<?php

header("Content-type: application/json; charset=utf-8");

require_once '../lib/Connect.php';
require_once '../class/LogsClass.php';
require_once '../class/RamaisClass.php';
require_once '../class/RamaisDatabaseClass.php';

$ramais         = new Ramais(file('../lib/ramais'), file('../lib/filas'));
$connect        = new Connect();
$log            = new LogsClass($connect->conn());
$ramaisDatabase = new RamaisDatabaseClass($connect->conn());


$search = $_GET['search'] ?? '';

if (isset($_GET['specificSearch'])) {

    $result = $ramais->searchRamais($_GET['specificSearch'], true);

} else if (in_array($search, array_values($ramais->statusRamais), true)) {

    $result = $ramais->searchRamais($search);

} else {

    $result = $ramais->getInfoRamais();

}

echo $result;

$ramaisDatabase->updateData();
$log->registerLog();

