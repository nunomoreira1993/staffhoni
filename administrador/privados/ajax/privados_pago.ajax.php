<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

$pago = intval($_POST['pago']);
$id = intval($_POST['id']);

if($id > 0 ){
    if($pago == 0){
        $pago = 1;
        $layer = "Pago";
    }
    else{
        $pago = 0;
        $layer = "Não pago";
    }

    $update = $db->Update('venda_privados', array('pago' => $pago) , "id=".$id);
    if($update){
        echo json_encode(array('sucesso' => 1, 'pago' => $pago, 'layer' => $layer));
        exit;
    }
}
echo json_encode(array('sucesso' => 0));
