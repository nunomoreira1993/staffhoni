<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);

if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$id_rp = intval($_GET['id_rp']);
$numero = intval($_GET['numero']);

if ($id_rp > 0 && $numero > 0 && $dbrps->verificaPresencaRP( $id_rp) == 0) {
    $campos['data_entrada'] = date('Y-m-d H:i:s');

    if (date('H') < 14) {
        $data = date('Y-m-d', strtotime('-1 day'));
    } else {
        $data = date('Y-m-d');
    }

    $campos['data_evento'] = $data;
    $campos['numero_cartao'] = $numero;
    $campos['id_rp'] = $id_rp;
    $campos['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $campos['ip'] = $_SERVER['REMOTE_ADDR'];

    $id = $db->Insert('presencas', $campos);
    if($id > 0){
        $db->Insert('logs', array('descricao' => "Adicionou uma entrada no RP ".$campos['id_rp'], 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));

        require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
        $dbpagamentos = new pagamentos($db);

        $camposExtra['descricao'] = "Valor da sessão - ".$data;
        $camposExtra['valor'] =  $dbpagamentos->devolveSessaoRP($id_rp);
        $camposExtra['id_rp'] = $id_rp;
        $camposExtra['id_presenca'] = $id;
        $camposExtra['sessao'] = 0;
        $camposExtra['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $camposExtra['ip'] = $_SERVER['REMOTE_ADDR'];
        $db->Insert( 'pagamentos_extras', $camposExtra);

        
        echo json_encode(array('sucesso' => 1,'erro' => 0));
    }
}
else{
    echo json_encode(array('erro' => 1, 'sucesso' => 0));
}
