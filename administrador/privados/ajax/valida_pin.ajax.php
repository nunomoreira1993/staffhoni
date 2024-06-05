<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$pin = intval($_POST['pin']);
$id_rp = intval($_POST['id_rp']);
if($id_rp > 0 && $pin > 0){
    if (strlen($pin) == 4){

        require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
        $dbrps = new rps($db);
        $rp = $dbrps->devolveRP( $id_rp);
        $pin_actual = $rp['pin'];
        if($pin_actual == 0){
            $db->Update('rps', array('pin' => $pin) , "id=".$id_rp);
            $_SESSION['id_processado'] = $id_rp;
            echo json_encode(array('sucesso' => 1));
        }
        else if($pin == $pin_actual) {
            $_SESSION['id_processado'] = $id_rp;
            echo json_encode(array('sucesso' => 1));
        }
        else{
            echo json_encode(array('erro' => "O pin introduzido não é o correcto.", 'sucesso' => 0));
        }
    }
    else{
        echo json_encode(array('erro' => "Introduza um PIN com 4 algarismos.", 'sucesso' => 0));
    }
}
else{
    echo json_encode(array('erro' => "Pin inválido.", 'sucesso' => 0));
}
