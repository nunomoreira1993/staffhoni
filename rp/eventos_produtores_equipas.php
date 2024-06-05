<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);
$equipas = $dbrp->listaEstatisticasEquipasProdutor($_GET['data_evento']);

?>

<div class="header">
	<h2>Estatisticas por equipa do evento de <?php echo date('d/m/Y', strtotime($_GET['data_evento'])); ?></h2>
</div>
<div class="entradas">
	<a href="/rp/index.php?pg=eventos_produtores" class="voltar">
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
							Total de entradas
						</span>
						<span class="valor">
							<?php echo (int) $equipa['entradas']; ?>
						</span>
					</span>

					<span class="item">
						<span class="titulo">
							Melhor Staff (Entradas)
						</span>
						<span class="valor">
							<?php echo $equipa['melhor_equipa_rp_entradas']; ?>
						</span>
					</span>

					<span class="item">
						<span class="titulo">
							Numero de Privados Vendidos
						</span>
						<span class="valor">
							<?php echo (int) $equipa['numero_privados']; ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Total de Vendas Privados
						</span>
						<span class="valor">
							<?php echo euro($equipa['total_vendas_privados']); ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Comissão Privados
						</span>
						<span class="valor">
							<?php echo euro($equipa['comissao_privados']); ?>
						</span>
					</span>
				</div>
				<div class="links">
					<?php
					if ($equipa['entradas'] > 0) {
					?>
						<a href="/rp/index.php?pg=eventos_equipas_rps&data_evento=<?php echo $_GET['data_evento']; ?>&id_equipa=<?php echo $equipa['rp']['id']; ?>" class="ver_entradas"> Ver entradas por staff </a>
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