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
							Total de entradas
						</span>
						<span class="valor">
							<?php echo $evento['entradas']; ?>
						</span>
					</span>

					<span class="item">
						<span class="titulo">
							Melhor Staff (Entradas)
						</span>
						<span class="valor">
							<?php echo $evento['melhor_equipa_rp_entradas']; ?>
						</span>
					</span>

					<span class="item">
						<span class="titulo">
							Numero de Privados Vendidos
						</span>
						<span class="valor">
							<?php echo (int) $evento['numero_privados']; ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Total de Vendas Privados
						</span>
						<span class="valor">
							<?php echo euro($evento['total_vendas_privados']); ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Comissão Privados
						</span>
						<span class="valor">
							<?php echo euro($evento['comissao_privados']); ?>
						</span>
					</span>
				</div>
				<div class="links">
					<?php
					if ($evento['entradas'] > 0) {
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