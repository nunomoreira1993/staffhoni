<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);

if ($_GET['id']) {
	$convite = $dbrp->devolveConvite(intval($_GET['id']));

	if (empty($convite)) {
		$_SESSION['erro'] = "Não é possivel aceder.";
		header('Location: /rp/index.php?pg=cartoes_consumo_obrigatorio');
		exit;
	}

	if (date('Y-m-d', strtotime('+3 day')) > $convite['data_evento']) {
		$_SESSION['erro'] = "Já não é possivel inserir convite para a data do evento inserido.";
		header('Location: /rp/index.php?pg=convites');
		exit;
	}
}

if ($_POST) {
	$id_rp = $_SESSION['id_rp'];
	$data_evento = $_POST['data_evento'];
	if (empty($data_evento)) {
		$_SESSION['erro'] = "Por favor introduza a data do evento.";
	}
	if (date('Y-m-d', strtotime('+3 day')) > $data_evento && empty($_SESSION['erro'])) {
		$_SESSION['erro'] = "Já não é possivel inserir convite para a data do evento inserido.";
	}
	if ($_FILES['imagem']['name'] && empty($_SESSION['erro'])) {

		if (empty($_FILES['imagem']['name'])) {
			$_SESSION['erro'] = "Insira a imagem do convite.";
		} else {
			$foto_importada = doUpload($_FILES['imagem'], "/convites/", "foto");
			if (empty($_SESSION['erro']) && !empty(trim($foto_importada))) {
				$imagem = $foto_importada;
			} else {
				$_SESSION['erro'] = $resize['errors']['user'][0];
			}
			if ($_GET['id'] && empty($_SESSION['erro'])) {
				if ($convite['imagem'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/convites/" . $convite['imagem'])) {
					unlink($_SERVER['DOCUMENT_ROOT'] . "/fotos/convites/" . $convite['imagem']);
				}
			}
		}
	}
	if ((empty($imagem) || !file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/convites/" . $imagem)) && empty($_SESSION['erro'])) {
		$_SESSION['erro'] = "Houve um erro a inserir a imagem, tente novamente.";
	}

	if (empty($_SESSION['erro'])) {
		$campos['data'] = date('Y-m-d H:i:s');
		$campos['data_evento'] = $data_evento;
		$campos['id_rp'] = $id_rp;

		$campos['imagem'] = $imagem;
		$campos['md5'] = md5_file($_SERVER['DOCUMENT_ROOT'] . "/fotos/convites/" . $imagem);

		$md5 = $dbrp->verificaMD5($campos['md5']);
		$md5 = 0;
		if ($md5 == 0) {
			if ($_GET['id']) {
				$db->Update('convites', $campos, 'id=' . intval($_GET['id']));
				$_SESSION['sucesso'] = "A convite foi alterado.";
			} else {
				$db->Insert('convites', $campos);
				$_SESSION['sucesso'] = "O convite foi inserido.";
			}
			header('Location: /rp/index.php?pg=convites');
			exit;
		} else {
			unlink($_SERVER['DOCUMENT_ROOT'] . "/fotos/convites/" . $imagem);
			$_SESSION['erro'] = "A imagem não pode ser inserida porque já foi usada anteriormente.";
		}
	}
	header('Location: /rp/index.php?pg=adicionar_convites&id=' . intval($_GET['id']));
	exit;
}
if (empty($_POST)) {
	$campos = $convite;
} else {

	$campos = $_POST;
}
?>


<div class="header">
	<h2>Convites </h2>
</div>

<div class="conteudo" <?php echo escreveErroSucesso(); ?>>
	<a href="/rp/index.php?pg=convites" class="voltar">
		<span class="icon"> <img src="/temas/rps/imagens/back.svg" /> </span>
		<span class="label"> Voltar </span>
	</a>

	<form name="formulario" class="consumo-obrigatorio" data-incremento="<?php echo count($campos['input']); ?>" action="" method="post" enctype="multipart/form-data">
		<div class="inputs">
			<div class="label">
				Data do evento
			</div>
			<div class="input">
				<input name="data_evento" value="<?php echo $campos['data_evento']; ?>" required="required" type="date" min="<?php echo date('Y-m-d', strtotime('+3 day')); ?>" />
			</div>
		</div>
		<div class="inputs">
			<div class="label">
				Foto
			</div>
			<div class="input">
				<?php
				if ($campos['imagem'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/convites/" . $campos['imagem'])) {
					?>
					<div class="foto">
						<img src="/fotos/convites/<?php echo $campos['imagem']; ?>" width="150px">
					</div>
				<?php
			}
			?>
				<input name="imagem" value="<?php echo $campos['imagem']; ?>" required="required" type="file" />
			</div>
		</div>
		<div class="inputs">
			<input type="submit" value="Enviar" />
		</div>
	</form>
</div>