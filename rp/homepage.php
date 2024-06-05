<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);
$rp = $dbrp->devolveInfo();
$eventos = $dbrp->listaEventosRP();

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
$pagamento = $dbpagamentos->devolvePagamento($_SESSION['id_rp']);
?>
<div class="perfil">
	<a href="#" class="foto">
		<img src="<?php echo $rp['foto']; ?>">
	</a>
	<span class="nome">
		<?php echo $rp['nome']; ?>
	</span>
	<span class="cargo">
		<?php echo $rp['nome_cargo']; ?>
	</span>
	<div class="conta-corrente-homepage">
		<div class="saldo">
			<span class="titulo">
				Saldo
			</span>
			<span class="valor <?php if ($pagamento['total'] < 0) { ?> pago <?php } else if ($pagamento['total'] > 0) { ?> recebido <?php } ?>">
				<?php
				echo euro($pagamento['total']);
				?>
			</span>
		</div>
		<a href="?pg=historico_pagamentos"> Ver histórico de pagamentos </a>
	</div>
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
							Entradas
						</span>
						<span class="valor">
							<?php echo $evento['quantidade']; ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Cartões sem consumo
						</span>
						<span class="valor">
							<?php echo $evento['cartoes_sem_consumo']; ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Embaixadores
						</span>
						<span class="valor">
							<?php echo $evento['cartoes_consumo_obrigatorio']; ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Comissão Entradas
						</span>
						<span class="valor">
							<?php echo euro($evento['comissao_entradas']); ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Comissão Bónus Entradas
						</span>
						<span class="valor">
							<?php echo euro($evento['comissao_bonus_entradas']); ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Comissão Entradas Equipa
						</span>
						<span class="valor">
							<?php echo euro($evento['comissao_equipa_entradas']); ?>
						</span>
					</span>
					<span class="item">
						<span class="titulo">
							Comissão Bónus Entradas Equipa
						</span>
						<span class="valor">
							<?php echo euro($evento['comissao_equipa_bonus_entradas']); ?>
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
					<span class="item">
						<span class="titulo">
							Comissão Garrafas
						</span>
						<span class="valor">
							<?php echo euro($evento['comissao_garrafas']); ?>
						</span>
					</span>
				</div>
			</div>
		<?php
	}
} else {
	?>
		<div class="sem_registos">
			Sem eventos a decorrer.
		</div>
	<?php
}
?>
</div>