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

$query = "SELECT id, nome FROM rps ";
$rps = $db->query($query);


$array[] = array("Data do evento: ", $data, "", "", "", "");
$array[] = array("Nome do STAFF", "Nº de Entradas", "Entradas - Comissão (€)", "Entradas - Bónus (€)", "Nº Entradas Equipa", "Entradas Equipa - Comissão (€)", "Entradas Equipa - Comissão Bónus (€)", "Nº Privados", "Privados - total (€)", "Privados - Comissão (€)", "Nº Privados Equipa", "Privados Equipa - Total (€)", "Privados Equipa - Comissão (€)");

require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
foreach ($rps as $rpp) {

    $dbrps2 = new rp($db, $rpp["id"]);
    $evento = $dbrps2 -> listaEventosEquipa($data)[0];

    $totalEntradas['entradas'] += $evento['entradas'];
    $totalEntradas['entradas_comissao'] += $evento['entradas_comissao'];
    $totalEntradas['entradas_bonus'] += $evento['entradas_bonus'];
    $totalEntradas['entradas_equipa'] += $evento['entradas_equipa'];
    $totalEntradas['entradas_equipa_comissao'] += $evento['entradas_equipa_comissao'];
    $totalEntradas['entradas_equipa_comissao_bonus'] += $evento['entradas_equipa_comissao_bonus'];
    $totalEntradas['privados_numero'] += $evento['privados_numero'];
    $totalEntradas['privados_total'] += $evento['privados_total'];
    $totalEntradas['privados_comissao'] += $evento['privados_comissao'];
    $totalEntradas['privados_equipa_numero'] += $evento['privados_equipa_numero'];
    $totalEntradas['privados_equipa_total'] += $evento['privados_equipa_total'];
    $totalEntradas['privados_equipa_comissao'] += $evento['privados_equipa_comissao'];

    $array[] = array(
        $rpp['nome'],
        $evento['entradas'],
        number_format($evento['entradas_comissao'], 2, ',', '.') . " €",
        number_format($evento['entradas_bonus'], 2, ',', '.') . " €",
        $evento["entradas_equipa"],
        number_format($evento["entradas_equipa_comissao"], 2, ',', '.') . " €",
        number_format($evento["entradas_equipa_comissao_bonus"], 2, ',', '.') . " €",
        $evento['privados_numero'],
        number_format($evento["privados_total"], 2, ',', '.') . " €",
        number_format($evento["privados_comissao"], 2, ',', '.') . " €",
        $evento['privados_equipa_numero'],
        number_format($evento["privados_equipa_total"], 2, ',', '.') . " €",
        number_format($evento["privados_equipa_comissao"], 2, ',', '.') . " €",
    );
}

$array[] = array(
    "Total",
    intval($totalEntradas['entradas']),
    number_format( $totalEntradas['entradas_comissao'], 2, ',', '.') . " €",
    number_format( $totalEntradas['entradas_bonus'], 2, ',', '.') . " €",
    intval($totalEntradas['entradas_equipa']),
    number_format( $totalEntradas['entradas_equipa_comissao'], 2, ',', '.') . " €",
    number_format( $totalEntradas['entradas_equipa_comissao_bonus'], 2, ',', '.') . " €",
    intval($totalEntradas['privados_numero']),
    number_format( $totalEntradas['privados_total'], 2, ',', '.') . " €",
    number_format( $totalEntradas['privados_comissao'], 2, ',', '.') . " €",
    intval($totalEntradas['privados_equipa_numero']),
    number_format( $totalEntradas['privados_equipa_total'], 2, ',', '.') . " €",
    number_format( $totalEntradas['privados_equipa_comissao'], 2, ',', '.') . " €"
);

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