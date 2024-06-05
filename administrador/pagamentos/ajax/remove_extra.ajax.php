<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);

if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

$id = intval($_POST['id']);
if($id){
    $id = $db->query("DELETE FROM pagamentos_extras WHERE id=".$id);
    if($id > 0){
        $db->Insert('logs', array('descricao' => "Alterou um extra do pagamento.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        echo json_encode(array('sucesso' => 1,'erro' => 0));
    }
}
else{
    echo json_encode(array('erro' => 1, 'sucesso' => 0));
}
