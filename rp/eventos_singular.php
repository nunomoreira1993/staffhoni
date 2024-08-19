<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);
$eventos = $dbrp->listaEventosEquipa();
?>

<div class="header">
	<h2>Os meus eventos</h2>
</div>
<div class="entradas">
	<?php
	if ($eventos) {
		foreach ($eventos as $evento) {
	?>
			<div class="evento">
				<div class="topo">
					<span class="data">
						<?php echo $evento['data_evento']; ?>
					</span>
					<span class="estado">
						<?php echo $evento['estado']; ?>
					</span>
				</div>
				<div class="rodape">

					<span class="item">
						<span class="titulo">
							Total de entradas (Individual)
						</span>
						<span class="valor">
							<?php echo (int) $evento['entradas']; ?>
						</span>
					</span>
					<?php
					if($evento['entradas'] > 0) {
					?>
						<span class="item">
							<span class="titulo">
								Posição (Entradas) (Individual)
							</span>
							<span class="valor">
								<?php echo $evento['estatisticas_individual']; ?> º Lugar
							</span>
						</span>

						<span class="item">
							<span class="titulo">
								Comissão (Individual)
							</span>
							<span class="valor">
								<?php echo euro($evento['entradas_comissao']); ?>
							</span>
						</span>

						<span class="item">
							<span class="titulo">
								Bónus (Individual)
							</span>
							<span class="valor">
								<?php echo euro($evento['entradas_bonus']); ?>
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
							<?php echo (int) $evento['privados_numero']; ?>
						</span>
					</span>
					<?php
					if($evento['privados_numero'] > 0) {
					?>
						<span class="item">
							<span class="titulo">
								Posição (Privados) (Individual)
							</span>
							<span class="valor">
								<?php echo $evento['estatisticas_privados']; ?> º Lugar
							</span>
						</span>
						<span class="item">
							<span class="titulo">
								Total de Vendas Privados (Individual)
							</span>
							<span class="valor">
								<?php echo euro($evento['privados_total']); ?>
							</span>
						</span>
						<span class="item">
							<span class="titulo">
								Comissão Privados (Individual)
							</span>
							<span class="valor">
								<?php echo euro($evento['privados_comissao']); ?>
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
			Sem registo de eventos a decorrer.
		</div>
	<?php
	}
	?>
</div>