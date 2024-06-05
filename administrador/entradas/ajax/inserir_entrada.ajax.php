<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$quantidade = intval($_POST['quantidade']);
$id_rp = intval($_POST['id_rp']);
if($id_rp > 0 && $quantidade > 0){
    $campos['data'] = date('Y-m-d H:i:s');

    if (date('H') < 14) {
        $data = date('Y-m-d', strtotime('-1 day'));
    } else {
        $data = date('Y-m-d');
    }
    
    $campos['data_evento'] = $data;
    $campos['id_rp'] = $_POST['id_rp'];
    $campos['quantidade'] = $_POST['quantidade'];
    $campos['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $campos['ip'] = $_SERVER['REMOTE_ADDR'];
    $id = $db->Insert('rps_entradas', $campos);
    if($id > 0){
        $db->Insert('logs', array('descricao' => "Inseriu uma entrada", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        echo json_encode(array('sucesso' => 1,'erro' => 0));
    }
}
else{
    echo json_encode(array('erro' => 1, 'sucesso' => 0));
}

?>