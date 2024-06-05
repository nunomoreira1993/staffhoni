<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";

$db->Insert('logs', array('descricao' => "Fez logout", 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Logout", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
unset($_SESSION['id_utilizador']);
unset($_SESSION['id_processado']);
header('Location:/index.php');
?>