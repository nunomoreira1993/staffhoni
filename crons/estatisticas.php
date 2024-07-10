<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/estatisticas/estatisticas.obj.php');
$dbestatisticas = new estatisticas($db);
$dbestatisticas->syncToStatistics();


