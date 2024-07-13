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
							Total de entradas (Individual)
						</span>
						<span class="valor">
							<?php echo (int) $equipa['entradas']; ?>
						</span>
					</span>
					<?php
					if($equipa['entradas'] > 0) {
						?>
						<span class="item">
							<span class="titulo">
								Posição (Entradas) (Individual)
							</span>
							<span class="valor">
								<?php echo $equipa['estatisticas_individual']; ?> º Lugar
							</span>
						</span>

						<span class="item">
							<span class="titulo">
								Comissão (Individual)
							</span>
							<span class="valor">
								<?php echo euro($equipa['entradas_comissao']); ?>
							</span>
						</span>

						<span class="item">
							<span class="titulo">
								Bónus (Individual)
							</span>
							<span class="valor">
								<?php echo euro($equipa['entradas_bonus']); ?>
							</span>
						</span>
						<?php
					}
					?>


					<span class="item">
						<span class="titulo">
							Numero de Privados (Individual)
						</span>
						<span class="valor">
							<?php echo (int) $equipa['privados_numero']; ?>
						</span>
					</span>
					<?php
					if($equipa['privados_numero'] > 0) {
						?>
						<span class="item">
							<span class="titulo">
								Posição (Privados) (Individual)
							</span>
							<span class="valor">
								<?php echo $equipa['estatisticas_privados']; ?> º Lugar
							</span>
						</span>
						<span class="item">
							<span class="titulo">
								Total de Vendas Privados (Individual)
							</span>
							<span class="valor">
								<?php echo euro($equipa['privados_total']); ?>
							</span>
						</span>
						<span class="item">
							<span class="titulo">
								Comissão Privados (Individual)
							</span>
							<span class="valor">
								<?php echo euro($equipa['privados_comissao']); ?>
							</span>
						</span>
						<?php
					}
					?>

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