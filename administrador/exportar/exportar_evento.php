<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

define('PEAR_PATH', $_SERVER['DOCUMENT_ROOT'].'/administrador/plugins/pear');
set_include_path($_SERVER['DOCUMENT_ROOT'].'/administrador/plugins/pear');

require_once PEAR_PATH . "/Spreadsheet/Excel/Writer.php";

$data = $_GET['data'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$entradasRPData = $dbrps->listaEntradasRPSDia($data);
$totalEntradas = $dbrps->listaEntradasDiasTotal($data);

$array[] = array("Data do evento: ", $data, "", "", "", "");
$array[] = array("Nome do STAFF", "Nº de Entradas", "Total S/Consumo", "Total de C/Obrigatório", "Total Entradas (€)", "Total V/ Privados (€)", "Total V/ Garrafas (€)");

foreach ($entradasRPData as $rpp) {
    $totalEntradas[0]['total_entradas'] += $rpp['total_entradas'];
    $totalEntradas[0]['total_privados'] += $rpp['total_privados'];
    $totalEntradas[0]['total_garrafas'] += $rpp['total_garrafas'];

    $array[] = array($rpp['nome'], $rpp['total'], $rpp['total_sem_consumo'], $rpp['total_cartoes_consumo_obrigatorio'], number_format($rpp[ 'total_entradas'], 2, ',', '.') . " €", number_format($rpp['total_privados'], 2, ',', '.') . " €", number_format( $rpp['total_garrafas'], 2, ',', '.')." €");
}

$array[] = array("Total", intval($totalEntradas[0]['total']), intval($totalEntradas[0]['total_sem_consumo']), intval($totalEntradas[0][ 'total_cartoes_consumo_obrigatorio']), number_format( $totalEntradas[0]['total_entradas'], 2, ',', '.') . " €", number_format( $totalEntradas[0]['total_privados'], 2, ',', '.') . " €", number_format($totalEntradas[0]['total_garrafas'], 2, ',', '.') . " €");
$nome_ficheiro =  "evento_".$_GET['data']."_entradas";
$workbook = new Spreadsheet_Excel_Writer();
$workbook->setVersion(8);
$ws1 = &$workbook->addWorksheet(forceFilename(strtolower( $nome_ficheiro)));
$ws1->setInputEncoding('UTF-8');
$ws1->setRow(0, 0);

foreach($array as $linha => $data){
    foreach($data as $conta => $celula){
        $ws1->write($linha, $conta, $celula);
    }
}

$workbook->send(strtolower( $nome_ficheiro). ".xls");
$workbook->close();

exit();