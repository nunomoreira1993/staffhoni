<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);

$pagamentos = $dbrp->devolvePagamentos();
?>
<div class="header">
	<h2>Historico de pagamentos</h2>
</div>

<div class="conteudo" <?php echo escreveErroSucesso(); ?>>
	<?php
	if (empty($pagamentos)) {
		?>
		<span class="sem_registos">
			Não foram encontrados registos.
		</span>
	<?php
} else {

	foreach ($pagamentos as $pagamento) {
		?>
			<div class="tabela pagamento">
				<div class="item">
					<div class="topo">
						<div class="coluna">
							<div class="titulo">
								Data
							</div>
							<div class="valor">
								<?php echo $pagamento['data']; ?>
							</div>
						</div>
						<div class="coluna">
							<div class="titulo">
								Valor
							</div>
							<div class="valor">
								<?php echo euro($pagamento['total']); ?>
							</div>
						</div>
						<a href="#" class="toggle mais"> Ver detalhe de pagamento </a>
						<a href="#" class="toggle menos"> Fechar detalhe de pagamento </a>
					</div>
					<div class="rodape">
						<?php
						foreach ($pagamento['linhas'] as $linha) {
							?>
							<div class="extra">
								<div class="topo_extra">
									<span class="nome">
										<?php echo $linha['nome']; ?>

									</span>
									<span class="valor <?php if ($linha['valor'] > 0) { ?>recebido<?php } else { ?>pago<?php } ?>">
										<?php echo euro($linha['valor']); ?>
									</span>
								</div>
								<span class="descricao">
									<?php echo $linha['descricao']; ?>

								</span>
							</div>
						<?php
					}
					?>

					</div>
				</div>
			</div>
		<?php
	}
}
?>
</div>