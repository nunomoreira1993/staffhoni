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
					<span class="item">
						<span class="titulo">
							Total de entradas (Equipa)
						</span>
						<span class="valor">
							<?php echo $evento['entradas_equipa']; ?>
						</span>
					</span>
					<?php
					if($evento['entradas_equipa'] > 0) {
					?>
						<span class="item">
							<span class="titulo">
								Posição (Entradas) (Equipa)
							</span>
							<span class="valor">
								<?php echo $evento['estatisticas_equipa']; ?> º Lugar
							</span>
						</span>
						<span class="item">
							<span class="titulo">
								Comissão (Equipa)
							</span>
							<span class="valor">
								<?php echo euro($evento['entradas_equipa_comissao']); ?>
							</span>
						</span>

						<span class="item">
							<span class="titulo">
								Bónus (Equipa)
							</span>
							<span class="valor">
								<?php echo euro($evento['entradas_equipa_comissao_bonus']); ?>
							</span>
						</span>
						<span class="item">
							<span class="titulo">
								Melhor Staff (Entradas) (Equipa)
							</span>
							<span class="valor">
								<?php echo $evento['melhor_equipa_rp_entradas']; ?>
							</span>
						</span>
					<?php
					}
					?>
					<span class="item">
						<span class="titulo">
							Numero de Privados (Equipa)
						</span>
						<span class="valor">
							<?php echo (int) $evento['privados_equipa_numero']; ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Total de Vendas Privados (Equipa)
						</span>
						<span class="valor">
							<?php echo euro($evento['privados_equipa_total']); ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Comissão Privados (Equipa)
						</span>
						<span class="valor">
							<?php echo euro($evento['privados_equipa_comissao']); ?>
						</span>
					</span>

				</div>
				<div class="links">
					<?php
					if ($evento['entradas_equipa'] > 0 || $evento['privados_equipa_numero'] > 0) {
					?>
						<a href="/rp/index.php?pg=eventos_equipas_rps&data_evento=<?php echo $evento['data_evento_sql']; ?>" class="ver_entradas"> Ver entradas por staff </a> <?php } ?>
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