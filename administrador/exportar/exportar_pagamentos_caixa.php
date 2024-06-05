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

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
$pagamentos = $dbpagamentos->listaExcellPagamentosCaixa($data);
$totalPagamentos = $dbpagamentos-> listaPagamentosCaixaData($data);

$array[] = array("Data do pagamento: ", $data, "", "", "", "");
$array[] = array(" ", " ", "", "", "", "");
$array[] = array("Nome", "Tipo", "Data", "Total (€)", "Caixa", "Pago por:");

foreach ( $pagamentos as $paga) {
    $array[] = array( $paga['nome'], $paga['tipo'], $paga['data'], number_format( $paga['total'], 2, ',', '.') . " €", $paga['pagamento_caixa'] == 1 ? "Sim" : "Não", $paga['nome_administrador']);
}
$array[] = array(" ", " ", "", "", "", "");
$array[] = array(" ", " ", "", "", "", "");
$array[] = array("Total Caixas (€)", "Total Pago/(caixa) (€)", "Diferença (T/Caixa - T/Pago)", "Total P/(Fora d/Caixa)", "Total em dívida (€)");

$array[] = array(number_format($totalPagamentos[0][ 'total_caixa'], 2, ',', '.') . " €", number_format($totalPagamentos[0][ 'total_pagamentos'], 2, ',', '.') . " €", number_format($totalPagamentos[0]['diferenca'], 2, ',', '.') . " €", number_format($totalPagamentos[0][ 'total_pagamentos_scaixa'], 2, ',', '.') . " €", number_format($totalPagamentos[0]['total_recebido'], 2, ',', '.') . " €");


$nome_ficheiro =  "pagamentos_" . $_GET['data'] . "";
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
