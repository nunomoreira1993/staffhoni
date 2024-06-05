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
$pagamentos = $dbpagamentos->listaExcellPagamentosLinhas($data);

$array[] = array("Staff", "Titulo", "Descrição", "Valor", "Caixa", "Pago por:");

foreach ( $pagamentos as $paga) {
    $array[] = array( $paga[ 'nome_staff'], $paga[ 'titulo_pagamento'], html_entity_decode(strip_tags($paga[ 'descricao_pagamento'])), number_format( $paga[ 'valor'], 2, ',', '.') . " €", $paga[ 'pagamento_caixa'] == 1 ? "Sim" : "Não", $paga[ 'nome_administrador']);
}


$nome_ficheiro =  "linhas_pagamentos_" . $_GET['data'] . "";
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
