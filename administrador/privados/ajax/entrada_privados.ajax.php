<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

if (date('H') < 14) {
    $data_evento = date('Y-m-d', strtotime('-1 day'));
} else {
    $data_evento = date('Y-m-d');
}

$id_mesa = intval($_POST['id_mesa']);
$cartoes = intval($_POST['cartoes']);

require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/privados/privados.obj.php');
$dbprivados = new privados($db);
$reserva = $dbprivados->devolveOcupacaoMesa( $id_mesa, $data_evento);

if ( $id_mesa > 0 && $cartoes > 0) {
    if(empty( $reserva)){

        $_SESSION['sucesso'] = "A mesa foi ocupada com sucesso.";
        $db->Insert('privados_salas_mesas_ocupacao', array('data_evento' => $data_evento, 'id_mesa' => $id_mesa, 'data' => date('Y-m-d H:i:s'), 'cartoes' => $cartoes));
        echo json_encode(array('sucesso' => 1));
    } else {

        $_SESSION['erro'] = "Não foi possivel adicionar a ocupação pois a mesa já se encontra ocupada.";
        echo json_encode(array('erro' => "A mesa já se encontra ocupada.", 'sucesso' => 0));
    }
} else {
    $_SESSION['erro'] = "Deve introduzir o numero de cartões para ocupar a mesa.";
    echo json_encode(array('erro' => "Deve introduzir o numero de cartões para ocupar a mesa.", 'sucesso' => 0));
}
