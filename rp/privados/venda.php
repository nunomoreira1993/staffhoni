<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/privados/privados.obj.php');
$dbprivados = new privados($db);

$_SESSION['id_processado'] = $_SESSION['id_rp'];
$_SESSION['id_utilizador'] = $dbprivados->devolveIDPrivado();

header('Location: /administrador/');
exit;

?>