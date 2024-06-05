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
$vendas = $dbprivados->listaVendaPrivados(true, $data);
$total = $dbprivados->totalVendaPrivados($data);

$array[] = array("Data do evento: ", $data, "", "", "", "");
$array[] = array("Codigo", "Área", "Mesa", "Data / Hora de venda", "Nome processado", "Nome do cliente", "Gerente", "Staff", "Valor Multibanco Adiantado", "Valor Dinheiro Adiantado", "Valor MBWAY Adiantado", "Total Adiantado", "Valor Multibanco", "Valor Dinheiro", "Valor MBWAY", "Total Pago Evento", "Total Camarote");
foreach ($vendas as $venda) {
    $array[] = array($venda['codigo'], $venda['sala'], $venda['mesa'], $venda['data'], $venda['nome_processado'], $venda['nome_cliente'], $venda['nome_gerente'], $venda['nome_rp'], number_format($venda['valor_multibanco_adiantado'], 2, ',', '.') . " €", number_format($venda['valor_dinheiro_adiantado'], 2, ',', '.') . " €", number_format($venda['valor_mbway_adiantado'], 2, ',', '.') . " €", number_format($venda['valor_multibanco_adiantado'] + $venda['valor_dinheiro_adiantado'] + $venda['valor_mbway_adiantado'], 2, ',', '.') . " €", number_format($venda['valor_multibanco'], 2, ',', '.') . " €", number_format($venda['valor_dinheiro'], 2, ',', '.') . " €", number_format($venda['valor_mbway'], 2, ',', '.') . " €", number_format($venda['valor_multibanco'] + $venda['valor_dinheiro'] + $venda['valor_mbway'], 2, ',', '.') . " €", number_format($venda['total'], 2, ',', '.') . " €");
}

$array[] = array("Total", "", "", "", "", "", "", "", number_format($total['valor_multibanco_adiantado'], 2, ',', '.') . " €", number_format($total['valor_dinheiro_adiantado'], 2, ',', '.') . " €", number_format($total['valor_mbway_adiantado'], 2, ',', '.') . " €", number_format($total['total_adiantado'], 2, ',', '.') . " €", number_format($total['valor_multibanco'], 2, ',', '.') . " €", number_format($total['valor_dinheiro'], 2, ',', '.') . " €", number_format($total['valor_mbway'], 2, ',', '.') . " €", number_format($total['total_evento'], 2, ',', '.') . " €", number_format($total['total'], 2, ',', '.') . " €");

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
