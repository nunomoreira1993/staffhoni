<?php 
if($_POST){
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
	$dbrp= new rp($db, $_SESSION['id_rp']);
	$passwordencode = $dbrp->devolvePassword();
	$password = base64_decode($passwordencode);
	$password_actual = $_POST['password_actual'];
	$password_nova = $_POST['password_nova'];
	$password_nova_repetir = $_POST['password_nova_repetir'];
	
	if($password == "" || $password_nova == "" || $password_nova_repetir == ""){
		$_SESSION['erro'] = $erro  = "Por favor preêncha todos os campos";
	}
	
	if(empty($erro) && $password_actual != $password){
		$_SESSION['erro'] = $erro = "A password actual não é válida. Por favor tente novamente";
	}
	
	if(empty($erro) && $password_nova != $password_nova_repetir){
		$_SESSION['erro'] = $erro  = "Os campos de novas password's não são iguais. Por favor tente novamente.";
	}
	if(empty($erro)){
		$sucesso = $db->Update('rps', array('password' => base64_encode($password_nova), 'alterou_password' => 1), 'id='.$_SESSION['id_rp']);
		if($sucesso){
			$_SESSION['sucesso']   = "A password foi alterada.";
			header('Location: /rp/');
			exit;
		}
	}
}
?>
<div class="header">
	<h2> Alterar password </h2>
</div>
<div class="conteudo" <?php echo escreveErroSucesso(); ?>>
	<form name="alterar" action="" method="post">
		<div class="inputs">
			<div class="label">
				Password actual
			</div>
			<div class="input">
				<input name="password_actual" value="<?php echo $password_actual; ?>" type="password" />
			</div>
		</div>

		<div class="inputs">
			<div class="label">
				Password Nova
			</div>
			<div class="input">
				<input name="password_nova" value="<?php echo $password_nova; ?>" type="password" />
			</div>
		</div>

		<div class="inputs">
			<div class="label">
				Repetir Password Nova
			</div>
			<div class="input">
				<input name="password_nova_repetir" value="<?php echo $password_nova_repetir; ?>" type="password" />
			</div>
		</div>
		<div class="inputs">
			<input type="submit" value="Alterar password" />
		</div>
	</form>
</div>