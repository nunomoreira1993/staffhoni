<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
$qrcode = $_REQUEST['qrcode'];

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);

if ($qrcode) {

    if (!preg_match('/^[a-f0-9]{32}$/i', $qrcode)) {
        $arr["status"] = "error";
        $arr["message"] = "QR Code inválido.";
        echo json_encode($arr);
        exit;
    }

    if (date('H') < 14) {
        $data = date('Y-m-d', strtotime('-1 day'));
    } else {
        $data = date('Y-m-d');
    }

    if (empty($_SESSION['id_utilizador'])) {
        header('Location:/index.php');
        exit;
    }

    $rp = $dbrps->getRPByQRCode($qrcode);

    $quantidade = 1;
    $id_rp = intval($rp["id"]);
    if ($id_rp > 0 && $quantidade > 0) {
        $campos['data'] = date('Y-m-d H:i:s');
        $campos['data_evento'] = $data;
        $campos['id_rp'] = $id_rp;
        $campos['quantidade'] = $quantidade;
        $campos['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $campos['ip'] = $_SERVER['REMOTE_ADDR'];
        $id = $db->Insert('rps_entradas', $campos);
        if ($id > 0) {
            $db->Insert('logs', array('descricao' => "Inseriu uma entrada via qrcode", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
            echo json_encode(array('status' => "success", "client_name" => $rp["nome"], "type" => "Bilhete"));
            exit;
        }
    } else {
        echo json_encode(array('status' => "error", "message" => "Ocorreu um problema a inserir a entrada (RP não encontrado)."));
        exit;
    }
}
else {

    echo json_encode(array('status' => "error", "message" => "Tentar novamente."));
    exit;
}
