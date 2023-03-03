<?php
header("Content-type: application/json; charset=utf-8");

require '../class/RamaisClass.php';

$r = new Ramais(file('../lib/ramais'), file('../lib/filas'));
echo $r->getQuantidadesStatusRamais();
