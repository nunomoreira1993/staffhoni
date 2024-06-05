<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/privados/privados.obj.php');
$dbprivados = new privados($db);
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);

$data_evento = $_GET['data_evento'];

if ($_GET['id']) {
	$lista_espera = $dbprivados->devolveListaEspera($data_evento, intval($_GET['id']));

	$campos['id_gerente'] = $lista_espera[0]['id_gerente'];
	$campos['id_rp'] = $lista_espera[0]['id_rp'];
	$campos['nome_cliente'] = $lista_espera[0]['nome_cliente'];
	$campos['data_evento'] = $lista_espera[0]['data_evento '];
	$campos['contacto'] = $lista_espera[0]['contacto'];
	$campos['tipo'] = (int) $lista_espera[0]['tipo'];
	
	if (empty($lista_espera)) {
		$_SESSION['erro'] = "Não é possivel aceder.";
		header('Location: /rp/index.php?pg=lista_espera_mesas');
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
    $nome_cliente = $_POST['nome_cliente'];
    $data_evento = $_GET['data_evento'];
    $contacto = $_POST['contacto'];
    $tipo = (int) $_POST['tipo'];

    if (empty($data_evento)) {
        $_SESSION['erro'] = "Por favor introduza a data do evento.";
    }
    if (empty($id_rp)) {
        $_SESSION['erro'] = "Por favor escolha um elemento do Staff para associar á lista de espera.";
    }
    if (empty($nome_cliente)) {
        $_SESSION['erro'] = "Por favor introduza o nome do cliente.";
	}
	if (empty($contacto)) {
		$_SESSION['erro'] = "Por favor introduza o contacto do cliente.";
	}
	if (empty($tipo)) {
		$_SESSION['erro'] = "Por favor introduza o tipo de reserva.";
	}

    if (empty($_SESSION['erro'])) {
        $campos['data'] = date('Y-m-d H:i:s');
        $campos['data_evento'] = $data_evento;
        $campos['id_rp'] = $id_rp;
        $campos['id_gerente'] = $id_gerente;
        $campos['nome_cliente'] = $nome_cliente;
        $campos['contacto'] = $contacto;
		$campos['tipo'] = $tipo;
		
        if ($_GET['id']) {
            $db->Update('venda_privados_lista_espera', $campos, 'id=' . intval($_GET['id']));
            $_SESSION['sucesso'] = "O cliente foi alterado.";
        } else {
			$id = $db->Insert('venda_privados_lista_espera', $campos);
			
            $_SESSION['sucesso'] = "O cliente foi adicionado na lista de espera.";
        }
        header('Location: /rp/index.php?pg=lista_espera_mesas&data_evento=' . $_GET['data_evento'] );
        exit;
    }
}

?>


<div class="header">
	<h2>Lista de espera - <?php echo $_GET['id'] > 0 ? ' Alterar' : 'Adicionar'; ?> </h2>
</div>

<div class="conteudo" <?php echo escreveErroSucesso(); ?>>
	<a href="/rp/index.php?pg=cartoes_sem_consumo" class="voltar">
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
				<input name="nome_cliente" value="<?php echo $campos['nome_cliente']; ?>" type="text" required="required" />
			</div>
		</div>
		<div class="inputs">
			<div class="label">
				Tipo
			</div>
			<div class="input">
				<select name="tipo" required="required">
					<option <?php if ($campos['tipo'] == 1) { ?> selected="selected" <?php } ?> value="1"> Camarote </option>
					<option <?php if ($campos['tipo'] == 2) { ?> selected="selected" <?php } ?> value="2"> Mesa </option>
				</select>
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
				Nº de Contacto
			</div>
			<div class="input">
				<input name="contacto" value="<?php echo $campos['contacto']; ?>" type="text" required="required" />
			</div>
		</div>

		<div class="inputs">
			<input type="submit" value="Enviar" />
		</div>
	</form>
</div>