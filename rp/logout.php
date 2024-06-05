<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
unset($_SESSION['id_rp']);
header('Location:/index.php');
?>