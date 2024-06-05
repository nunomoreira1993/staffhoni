<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);
if ($_GET['id_equipa']) {
	$link_retorno = "/rp/index.php?pg=eventos_produtores_equipas&data_evento=" . $_GET['data_evento'];
} else {
	$_GET['id_equipa'] = $_SESSION['id_rp'];
	$link_retorno = "/rp/index.php?pg=eventos_equipa";
}
$equipas = $dbrp->listaEstatisticasStaffEquipa($_GET['data_evento'], (int) $_GET['id_equipa']);
?>

<div class="header">
	<h2>Estatisticas por staff da equipa "<?php echo $dbrp->devolveNomeRp($_GET['id_equipa']); ?>" do evento de <?php echo date('d/m/Y', strtotime($_GET['data_evento'])); ?></h2>
</div>
<div class="entradas">
	<a href="<?php echo $link_retorno; ?>" class="voltar">
		<span class="icon"> <img src="/temas/rps/imagens/back.svg" /> </span>
		<span class="label"> Voltar </span>
	</a>
	<?php
	if ($equipas) {
		foreach ($equipas as $equipa) {

	?>
			<div class="evento">
				<div class="topo">
					<span class="foto">
						<img src="/fotos/rps/<?php echo $equipa['rp']['foto']; ?>" />
					</span>
					<span class="nome">
						<?php echo $equipa['rp']['nome']; ?>
					</span>
				</div>
				<div class="rodape">
					<span class="item">
						<span class="titulo">
							Total - Entradas
						</span>
						<span class="valor">
							<?php echo (int) $equipa['entradas']; ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Comissão - Entradas
						</span>
						<span class="valor">
							<?php echo euro($equipa['entradas_comissao']); ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Bonus - Entradas
						</span>
						<span class="valor">
							<?php echo euro($equipa['entradas_comissao_bonus']); ?>
						</span>
					</span>
					<?php
					if ($equipa['entradas_equipa']) {
					?>
						<span class="item">
							<span class="titulo">
								Total - Equipa - Entradas
							</span>
							<span class="valor">
								<?php echo ($equipa['entradas_equipa']); ?>
							</span>
						</span>
						<span class="item">
							<span class="titulo">
								Comissão - Equipa - Entradas
							</span>
							<span class="valor">
								<?php echo euro($equipa['entradas_equipa_comissao']); ?>
							</span>
						</span>
						<span class="item">
							<span class="titulo">
								Bonus - Equipa - Entradas
							</span>
							<span class="valor">
								<?php echo euro($equipa['entradas_equipa_comissao_bonus']); ?>
							</span>
						</span>
					<?php
					}
					?>
					<span class="item">
						<span class="titulo">
							Comissão Privados
						</span>
						<span class="valor">
							<?php echo euro($equipa['comissao_privados']); ?>
						</span>
					</span>
				</div>
			</div>
		<?php
		}
	} else {
		?>
		<div class="sem_registos">
			Sem registo de equipas para este evento.
		</div>
	<?php
	}
	?>
</div>