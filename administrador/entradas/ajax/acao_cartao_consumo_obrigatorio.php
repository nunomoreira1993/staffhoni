<?php 

include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
$acao = $_POST['acao'];
$id = $_POST['id'];
if($acao == "confirmar"){
    $retorno = $db->Update('rps_cartoes_consumo_obrigatorio', array('entrou' => 1), 'id='.$id);
}
else{
    $retorno = $db->Update('rps_cartoes_consumo_obrigatorio', array('entrou' => 0), 'id=' . $id);
}
echo json_encode(array('retorno' => $retorno));
?>