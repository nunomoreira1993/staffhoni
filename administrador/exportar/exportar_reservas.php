<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_rp'])) {
    header('Location:/index.php');
    exit;
}

define('PEAR_PATH', $_SERVER['DOCUMENT_ROOT'] . '/administrador/plugins/pear');
set_include_path($_SERVER['DOCUMENT_ROOT'] . '/administrador/plugins/pear');

require_once PEAR_PATH . "/Spreadsheet/Excel/Writer.php";

$data = $_GET['data'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/privados/privados.obj.php');
$dbprivados = new privados($db);
$vendas = $dbprivados->devolveReservasMesas($data);

$total['valor'] = 0;
$total['valor_multibanco_adiantado'] = 0;
$total['valor_dinheiro_adiantado'] = 0;

$array[] = array("Data do evento: ", $data, "", "", "", "");
$array[] = array("Mesa", "Nome", "Garrafas", "Cartões", "Valor", "Valor Multibanco (adiantado)", "Valor Dinheiro (adiantado)", "Valor MBWAY (adiantado)", "Total (adiantado)");

foreach ($vendas as $venda) {
    $array[] = array($venda['codigo_mesa'], $venda['nome'], $venda['garrafas'], $venda['cartoes'], number_format($venda['valor'], 2, ',', '.'), number_format($venda['valor_multibanco_adiantado'], 2, ',', '.') . " €", number_format($venda['valor_dinheiro_adiantado'], 2, ',', '.') . " €", number_format($venda['valor_mbway_adiantado'], 2, ',', '.') . " €", number_format($venda['valor_multibanco_adiantado'] + $venda['valor_dinheiro_adiantado'] + $venda['valor_mbway_adiantado'], 2, ',', '.') . " €");

    $total['valor'] += $venda['valor'];
    $total['valor_multibanco_adiantado']  += $venda['valor_multibanco_adiantado'];
    $total['valor_dinheiro_adiantado']  += $venda['valor_dinheiro_adiantado'];
    $total['valor_mbway_adiantado']  += $venda['valor_mbway_adiantado'];
}

$array[] = array("Total:", "", "", "", number_format($total['valor'], 2, ',', '.'), number_format($total['valor_multibanco_adiantado'], 2, ',', '.') . " €", number_format($total['valor_dinheiro_adiantado'], 2, ',', '.') . " €", number_format($total['valor_mbway_adiantado'], 2, ',', '.') . " €", number_format($total['valor_multibanco_adiantado'] + $total['valor_dinheiro_adiantado'] + $total['valor_mbway_adiantado'], 2, ',', '.') . " €");

$nome_ficheiro =  "reservas_" . $_GET['data'] . "_privados";
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
