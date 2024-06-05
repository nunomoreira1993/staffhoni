<?php
class administrador {
	function __construct($db) {
		$this->db = $db;
	}
	function verificaAdministrador($username, $password){
		require_once($_SERVER['DOCUMENT_ROOT'].'/validacao/validacao.obj.php');
		$dbvalidacao = new validacao($db);
		$erro = $dbvalidacao -> valida_email($username, "E-mail");
		if($username == "mendes"){
			unset($erro);
		}
		if($erro){
			$erroTel = $dbvalidacao->valida_inteiro($username, "Télemovel");
			if($erroTel){
				$erro = "Por favor digite um e-mail válido ou um número de telemóvel válido para iniciar sessão.";
			}
			else{
				unset($erro);
			}
		}

		if(empty($erro)){
			$password = trim($password);
			$password = base64_encode($password);
			$query ="SELECT count(*) as conta FROM administradores WHERE (administradores.email = '".$username."' OR administradores.telemovel ='".$username."') and administradores.password = '$password' ";
			$res = $this->db->query($query);
			if($res[0]['conta'] == 0){
				$erro = "Os dados não estão correctos";
			}
		}
		return $erro;
	}
	function setSession($login, $password){
		$password = trim($password);
		$password = base64_encode($password);

		$query ="SELECT * FROM administradores WHERE (administradores.email = '".$login."' OR administradores.telemovel ='" . $login . "') and administradores.password = '$password' ";
		$res = $this->db->query($query);
		$_SESSION['id_utilizador'] = $res[0]['id'];
		$this->db->Insert('logs', array('descricao' => "Fez login", 'arr' => json_encode($res), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Login"));
		return $res;
	}
	function devolveUtilizador($id){
		$query ="SELECT * FROM administradores WHERE administradores.id = '$id'";
		$res = $this->db->query($query);
		return $res[0];
	}
	function listaUtilizadores($pag=false){
		$query ="SELECT * FROM administradores WHERE id != ".$_SESSION['id_utilizador']." AND id != 16 AND id != 1 ORDER BY id, nome $pag";
		$res = $this->db->query($query);
		return $res;
	}
	function contaUtilizadores(){
		$query ="SELECT count(*) as conta FROM administradores WHERE tipo=2";
		$res = $this->db->query($query);
		return $res[0]['conta'];
	}
}
