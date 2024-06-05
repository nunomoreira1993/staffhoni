<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/privados/privados.obj.php');
$dbprivados = new privados($db);
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);

$data_evento = $_GET['data_evento'];

if ($_GET['id']) {
	$lista_espera = $dbprivados->devolveListaEsperaGarrafas($data_evento, intval($_GET['id']));

	$campos['id_gerente'] = $lista_espera[0]['id_gerente'];
	$campos['id_rp'] = $lista_espera[0]['id_rp'];
	$campos['nome'] = $lista_espera[0]['nome'];
	$campos['data_evento'] = $lista_espera[0]['data_evento'];
	$campos['valor'] = $lista_espera[0]['valor'];
	$campos['garrafas']  = $lista_espera[0]['garrafas'];
	foreach ($campos['garrafas'] as $garr_venda) {
		$garrafas_escolhidas[$garr_venda['id_garrafa']] = $garr_venda['quantidade'];
	}
	if (empty($lista_espera)) {
		$_SESSION['erro'] = "Não é possivel aceder.";
		header('Location: /rp/index.php?pg=reserva_garrafas');
		exit;
	}
}

$permissao = $dbrp->permissao();
if ($permissao == 0) {
	header('Location: /rp/index.php');
}

$rps = $dbrp->listaRps();

if ($_POST) {
	$id_gerente = $_SESSION['id_rp'];
	$id_rp = $_POST['id_rp'];
	$nome = $_POST['nome'];
	$garrafas = $_POST['garrafas'];
	$data_evento = $_GET['data_evento'];
	$valor = $_POST['valor'];

	if (empty($data_evento)) {
		$_SESSION['erro'] = "Por favor introduza a data do evento.";
	}
	if (empty($nome)) {
		$_SESSION['erro'] = "Por favor introduza o nome do cliente.";
	}
	if (empty($id_rp)) {
		$_SESSION['erro'] = "Por favor escolha um elemento do Staff para associar á lista de espera.";
	}
	if (empty($garrafas)) {
		$_SESSION['erro'] = "Por favor escolha as garrafas da reserva.";
	}
	if (empty($valor)) {
		$_SESSION['erro'] = "Por favor introduza o valor da reserva.";
	}
	$campos['data_evento'] = $data_evento;
	$campos['id_rp'] = $id_rp;
	$campos['nome'] = $nome;
	$campos['valor'] = $valor;
	if (empty($_SESSION['erro'])) {
		$campos['data'] = date('Y-m-d H:i:s');
		$campos['id_processado'] = $_SESSION['id_rp'];
		$id_reserva = $_GET['id'];
		if ($_GET['id']) {
			$db->Update('reserva_garrafas', $campos, 'id=' . intval($_GET['id']));
			$_SESSION['sucesso'] = "O cliente foi alterado.";
		} else {
			$id_reserva = $db->Insert('reserva_garrafas', $campos);
			$_SESSION['sucesso'] = "O cliente foi adicionado na lista de espera.";
		}

		if (empty($_SESSION['erro'])) {
			foreach ($_POST['garrafas'] as $id_garrafa => $quantidade) {
				$garrafa_campos['id_reserva'] = $id_reserva;
				$garrafa_campos['id_garrafa'] = $id_garrafa;
				$garrafa_campos['quantidade'] = $quantidade;
				$db->Insert('reserva_garrafas_garrafas', $garrafa_campos);
			}
		}
		header('Location: /rp/index.php?pg=reserva_garrafas&data_evento=' . $_GET['data_evento']);
		exit;
	}
}

?>


<div class="header">
	<h2>Lista de espera - <?php echo $_GET['id'] > 0 ? ' Alterar' : 'Adicionar'; ?> </h2>
</div>

<div class="conteudo" <?php echo escreveErroSucesso(); ?>>
	<a href="/rp/index.php?pg=reserva_garrafas" class="voltar">
		<span class="icon"> <img src="/temas/rps/imagens/back.svg" /> </span>
		<span class="label"> Voltar </span>
	</a>

	<form name="formulario" class="consumo-obrigatorio" data-incremento="<?php echo count($campos['input']); ?>" action="" method="post">
		<div class="inputs">
			<div class="label">
				Data do evento
			</div>
			<div class="input">
				<?php echo $data_evento; ?>
			</div>
		</div>

		<div class="inputs">
			<div class="label">
				Nome do cliente
			</div>
			<div class="input">
				<input name="nome" value="<?php echo $campos['nome']; ?>" type="text" required="required" />
			</div>
		</div>

		<div class="inputs">
			<div class="label">
				Staff
			</div>
			<div class="input">
				<select name="id_rp" required="required">
					<option value="">
						Selecione um Staff
					</option>
					<?php
					foreach ($rps as $rp) {
						?>
						<option value="<?php echo $rp['id']; ?>" <?php if ($campos['id_rp'] == $rp['id']) { ?> selected="selected" <?php } ?>><?php echo $rp['nome']; ?> </option>
					<?php
					}
					?>
				</select>
			</div>
		</div>

		<div class="inputs">
			<div class="label">
				Garrafas
			</div>
			<div class="input garrafas">
				<div class="escolha-garrafas-responsive">
					<?php
					include $_SERVER['DOCUMENT_ROOT'] . "/rp/privados/inputs_garrafas.html.php";
					?>
				</div>
				<a data-fancybox data-type="ajax" data-src="/administrador/privados/ajax/garrafas.ajax.php" href="javascript:;" class="adicionar">Adicionar garrafas </a>
			</div>
		</div>

		<div class="inputs">
			<div class="label">
				Valor (€)
			</div>
			<div class="input">
				<input name="valor" value="<?php echo $campos["valor"]; ?>" type="number" step="0.01" />
			</div>
		</div>

		<div class="inputs">
			<input type="submit" value="Enviar" />
		</div>
	</form>
</div>