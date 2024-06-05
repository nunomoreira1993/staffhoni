<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);

$permissao = $dbrp->permissao();

if($_GET['id']){
	$cartao = $dbrp->devolveCartaoConsumoObrigatorio(intval($_GET['id']));
	if(empty($cartao)){
		$_SESSION['erro'] = "Não é possivel aceder.";
		header('Location: /rp/index.php?pg=cartoes_consumo_obrigatorio');
		exit;
	}
}
if($_POST){
	$id_rp = $_SESSION['id_rp'];
	if($_POST['input']){
		$data_evento = $_POST['data_evento'];
		if(empty($data_evento)){
			$_SESSION['erro'] = "Por favor introduza a data do evento.";
		}
		if(empty($_SESSION['erro'])){
			foreach($_POST['input'] as $inputs){
				if($permissao == 0){
					$conta = 	$dbrp->validaCartaoObrigatorio($data_evento, intval($_GET['id']));
					$total = $conta + count($_POST['input']);
					if($total > 2){
						$_SESSION['erro'] = "Não pode atribuir mais de 2 cartões de consumo obrigatório para a data ".date('d-m-Y', strtotime($data_evento))."";
					}
				}
				else if($conta <= 2){
					if(empty($inputs['nome'])){
						$_SESSION['erro'] = "Por favor preêncha o campo nome, ou apague o campo";
					}
				}
			}
		}

		if (empty($_SESSION['erro'])) {
			foreach ($_POST['input'] as $inputs) {
				$campos['data_evento'] = $data_evento;
				$campos['id_rp'] = $id_rp;
				$campos['nome'] = $inputs['nome'];
				if ($_GET['id']) {
					$db->Update('rps_cartoes_consumo_obrigatorio', $campos, 'id=' . intval($_GET['id']));
					$_SESSION['sucesso'] = "A password foi alterada.";
				} else {
					$db->Insert('rps_cartoes_consumo_obrigatorio', $campos);
					$_SESSION['sucesso'] = "Os nomes foram adicionados";
				}
			}
			header('Location: /rp/index.php?pg=cartoes_consumo_obrigatorio');
			exit;
		}
	}
}
if(empty($_POST)){
	$data_evento = $cartao['data_evento'];
	$campos = array('input' => array(0 => array('nome' => $cartao['nome'])));
}
else{
	$campos = $_POST;
}
?>


<div class="header">
	<h2>Embaixadores </h2>
</div>

<div class="conteudo" <?php echo escreveErroSucesso(); ?>>
	<a href="/rp/index.php?pg=cartoes_consumo_obrigatorio" class="voltar">
		<span class="icon"> <img src="/temas/rps/imagens/back.svg"/> </span>
		<span class="label"> Voltar </span>
	</a>

	<form name="formulario" class="consumo-obrigatorio" data-incremento="<?php echo count($campos['input']); ?>" action="" method="post">

		<div class="inputs">
			<div class="label">
				Data do evento
			</div>
			<div class="input">
				<input name="data_evento" value="<?php echo $data_evento; ?>"  required="required" type="date" min="<?php echo date('Y-m-d', strtotime('-1 day')); ?>" />
			</div>
		</div>
		<?php
		foreach($campos['input'] as $k => $campo){
			?>
			<div class="bloco">
				<?php
				if($k > 0){
					?>
					<a href="#" class="remover">
						<img src="/temas/rps/imagens/remover.svg"/>
					</a>
				<?php
				}
				?>
				<div class="inputs">
					<div class="label">
						Nome do cliente
					</div>
					<div class="input">
						<input name="input[<?php echo $k; ?>][nome]" value="<?php echo $campo['nome']; ?>" type="text" required="required" />
					</div>
				</div>
			</div>

			<?php
		}
		if($_GET['id'] == 0){
			?>
			<div class="inputs">
				<input type="button" class="adicionar_mais" value="Adicionar mais" />
			</div>
			<?php
		}
		?>
		<div class="inputs">
			<input type="submit" value="Enviar" />
		</div>
	</form>
</div>

