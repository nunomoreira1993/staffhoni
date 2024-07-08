<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

define('PEAR_PATH', $_SERVER['DOCUMENT_ROOT'] . '/administrador/plugins/pear');
set_include_path($_SERVER['DOCUMENT_ROOT'] . '/administrador/plugins/pear');

require_once PEAR_PATH . "/Spreadsheet/Excel/Writer.php";

$data = $_GET['data'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);
$vendas = $dbprivados->devolveGarrafaVendaPrivadosData($data);


$array[] = array("Data do evento: ", $data, "", "", "", "");
if($vendas) {
    $array[] = array("Vendas de garrafas em privados", "","", "", "", "");
    $array[] = array("Garrafa", "Quantidade");
    foreach ($vendas as $venda) {
        $array[] = array($venda['nome'], $venda['quantidade']);
    }
}

$vendas = $dbprivados->devolveGarrafaVendaGarrafasData($data);
if($vendas) {
    $array[] = array("", "", "", "", "", "");
    $array[] = array("", "", "", "", "", "");
    $array[] = array("", "", "", "", "", "");
    $array[] = array("Vendas de Garrafas", "","", "", "", "");
    $array[] = array("Garrafa", "Quantidade");
    foreach ($vendas as $venda) {
        $array[] = array($venda['nome'], $venda['quantidade']);
    }
}

$nome_ficheiro =  "vendas_" . $_GET['data'] . "_privados";
$workbook = new Spreadsheet_Excel_Writer();
$workbook->setVersion(8);
$ws1 = &$workbook->addWorksheet(forceFilename(strtolower($nome_ficheiro)));
$ws1->setInputEncoding('UTF-8');
$ws1->setRow(0, 0);

foreach ($array as $linha => $data) {
    foreach ($data as $conta => $celula) {
        $ws1->write($linha, $conta, $celula);
    }
}

$workbook->send(strtolower($nome_ficheiro) . ".xls");
$workbook->close();

exit();
