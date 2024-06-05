<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);

if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$id_rp = intval($_POST['id_rp']);
$nome = ($_POST[ 'nome']);
$id = intval($_POST['id']);

if ($id_rp > 0 || $nome) {

    $campos['nome'] = $_POST['nome'];
    $campos['tipo'] = $_POST[ 'tipo'];
    $campos['descricao'] = $_POST['descricao'];
    $campos['valor'] = $_POST['valor'];
    $campos['id_rp'] = $_POST['id_rp'];
    $campos['sessao'] = $_POST['sessao'];
    $campos['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $campos['ip'] = $_SERVER['REMOTE_ADDR'];

    if($id){
        $alterado = $db->Update( 'pagamentos_extras', $campos, "id=".$id);
        
        if($alterado > 0){
            $db->Insert('logs', array('descricao' => "Alterou um extra do pagamento.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
            echo json_encode(array('sucesso' => $id,'erro' => 0));
        }
    }
    else{

        $id = $db->Insert( 'pagamentos_extras', $campos);
        if($id > 0){
            $db->Insert('logs', array('descricao' => "Alterou um extra do pagamento.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
            echo json_encode(array('sucesso' => $id,'erro' => 0));
        }
    }
}
else{
    echo json_encode(array('erro' => 1, 'sucesso' => 0));
}
