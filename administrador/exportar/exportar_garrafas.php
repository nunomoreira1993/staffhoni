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
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);
$vendas = $dbprivados->listaVendaGarrafas(true, $data);
$total = $dbprivados->totalVendaGarrafas($data);

$array[] = array("Data do evento: ", $data, "", "", "", "");
$array[] = array( "Codigo", "Nome processado", "Nome do cliente", "Staff", "Valor Multibanco", "Valor Dinheiro", "Total");
foreach ( $vendas as $venda) {
    $array[] = array( $venda['codigo'], $venda[ 'nome_processado'], $venda['nome_cliente'], $venda[ 'nome_rp'], number_format($venda['valor_multibanco'], 2, ',', '.') . " €", number_format($venda['valor_dinheiro'], 2, ',', '.') . " €", number_format($venda[ 'total'], 2, ',', '.') . " €");
}

$array[] = array("Total", "", "", "", number_format($total['valor_multibanco'], 2, ',', '.') . " €", number_format( $total['valor_dinheiro'], 2, ',', '.') . " €", number_format( $total['total'], 2, ',', '.') . " €");

$nome_ficheiro =  "vendas_".$_GET['data']."_privados";
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