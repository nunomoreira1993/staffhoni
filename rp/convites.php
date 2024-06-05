<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);


$convites = $dbrp->devolveConvites();

if ($_GET['apagar'] && $_GET['id']) {
	$cartao = $dbrp->devolveConvite(intval($_GET['id']));
	if (empty($cartao)) {
		$_SESSION['erro'] = "Não é possivel apagar o convite";
	}
	if (empty($_SESSION['erro'])) {
		$dbrp->apagaConvite(intval($_GET['id']));
		$_SESSION['sucesso'] = "O convite foi apagado com sucesso.";
		header('Location: /rp/index.php?pg=convites');
		exit;
	}
}
?>
<div class="header">
	<h2>Convites </h2>
</div>

<div class="conteudo" <?php echo escreveErroSucesso(); ?>>
	<a href="/rp/index.php?pg=adicionar_convites&id=0" class="adicionar">
		<span class="icon"> <img src="/temas/rps/imagens/adicionar.svg" /> </span>
		<span class="label"> Inserir Convite </span>
	</a>
	<?php
	if (empty($convites)) {
		?>
		<span class="sem_registos">
			Não foram encontrados registos.
		</span>
	<?php
} else {

	foreach ($convites as $convite) {
		?>
			<div class="tabela">
				<div class="item">
					<div class="topo">
						<div class="coluna">
							<div class="titulo">
								Data do evento
							</div>
							<div class="valor">
								<?php echo $convite['data_evento']; ?>
							</div>
						</div>

						<div class="coluna">
							<div class="titulo">
								Imagem
							</div>
							<div class="valor">
								<img src="/fotos/convites/<?php echo $convite['imagem']; ?>" />
							</div>
						</div>
					</div>
					<?php
					if (date('Y-m-d', strtotime('+3 day')) <= $convite['data_evento']) {
						?>
						<div class="rodape">
							<a href="/rp/index.php?pg=adicionar_convites&id=<?php echo $convite['id']; ?>" class="editar"> Editar </a>
							<a href="/rp/index.php?pg=convites&id=<?php echo $convite['id']; ?>&apagar=1" class="apagar"> Apagar </a>
						</div>
					<?php
				}
				?>
				</div>
			</div>
		<?php
	}
}
?>
</div>