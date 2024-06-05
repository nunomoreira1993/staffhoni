<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);

$select = "SELECT id FROM rps ORDER BY data_sync_valor_receber ASC  LIMIT 100";
$rps = $db->query($select);
foreach($rps as $rp){
	$total = $dbpagamentos->devolvePagamento($rp['id'])['total'];
	$db->update('rps', array('data_sync_valor_receber' => date('Y-m-d H:i:s'), 'valor_receber' => $total), 'id='.$rp['id']);
}