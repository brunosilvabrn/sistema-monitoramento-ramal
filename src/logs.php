<?php

header("Content-type: application/json; charset=utf-8");

require_once '../lib/Connect.php';
require_once '../class/LogsClass.php';

$connect = new Connect();
$logs =    new LogsClass($connect->conn());

echo $logs->getLogsDB();