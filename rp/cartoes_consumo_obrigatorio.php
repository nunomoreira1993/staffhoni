<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);
$cartoes_consumo_obrigatorio = $dbrp->devolveCartoesConsumoObrigatorio();
if($_GET['apagar'] && $_GET['id']){
	$cartao = $dbrp-> devolveCartaoConsumoObrigatorio(intval($_GET['id']));
	if(empty($cartao)){
		$_SESSION['erro'] = "Não é possivel apagar o cartão";
	}
	if(empty($_SESSION['erro'])){
		$dbrp->apagaCartaoConsumoObrigatorio(intval($_GET['id']));
		$_SESSION['sucesso'] = "O cartão foi apagado com sucesso.";
		header('Location: /rp/index.php?pg=cartoes_consumo_obrigatorio');
		exit;
	}
}
?>
<div class="header">
	<h2>Embaixadores </h2>
</div>

<div class="conteudo" <?php echo escreveErroSucesso(); ?>>
	<a href="/rp/index.php?pg=adicionar_cartoes_consumo_obrigatorio&id=0" class="adicionar">
		<span class="icon"> <img src="/temas/rps/imagens/adicionar.svg"/> </span>
		<span class="label"> Inserir cartão </span>
	</a>
	<?php
	if(empty($cartoes_consumo_obrigatorio)){
		?>
		<span class="sem_registos">
			Não foram encontrados registos.
		</span>
		<?php
	}
	else{

		foreach($cartoes_consumo_obrigatorio as $cartoes){
		?>
			<div class="tabela">
				<div class="item">
					<div class="topo">
						<div class="coluna">
							<div class="titulo">
								Nome
							</div>
							<div class="valor">
								<?php echo $cartoes['nome']; ?>
							</div>
						</div>
						<div class="coluna">
							<div class="titulo">
								Data
							</div>
							<div class="valor">
								<?php echo $cartoes['data_evento']; ?>
							</div>
						</div>
					</div>
					<?php
					if($cartoes['entrou'] == 0 && $cartoes['data_evento'] >= date('Y-m-d', strtotime('-1 day'))){
						?>
						<div class="rodape">
							<a href="/rp/index.php?pg=adicionar_cartoes_consumo_obrigatorio&id=<?php echo $cartoes['id']; ?>" class="editar"> Editar </a>
							<a href="/rp/index.php?pg=cartoes_consumo_obrigatorio&id=<?php echo $cartoes['id']; ?>&apagar=1" class="apagar"> Apagar </a>
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