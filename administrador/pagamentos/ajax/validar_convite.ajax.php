<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$id = intval($_GET['id']);

if ( $id > 0) {
    $campos[ 'valido'] = intval($_GET['valido']);
    $id = $db->Update('convites', $campos, "id = '".$id."'");
    if($id > 0){
        $db->Insert('logs', array('descricao' => "Validou / Recusou um convite ao efectuar pagamento.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        echo json_encode(array('sucesso' => 1,'erro' => 0));
    }
}
else{
    echo json_encode(array('erro' => 1, 'sucesso' => 0));
}
