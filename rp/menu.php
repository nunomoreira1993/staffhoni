<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);
$permissao = $dbrp->permissao();
$cargo = $dbrp->cargo();
?>
<div class="header">
	<a href="#" class="back">
		<img src="/temas/rps/imagens/back.svg" />
	</a>
	<h2> Definições </h2>
	<a href="/rp/logout.php" class="logout">
		<img src="/temas/rps/imagens/logout.svg" />
	</a>
</div>

<div class="tab">
	<div class="items">
		<a href="/rp/index.php">
			<span class="icon"><img src="/temas/rps/imagens/home.svg" /></span>
			<span class="nome">Página Inicial</span>
		</a>
	</div>
</div>
<div class="tab">
	<h3 class="titulo"> Staff </h3>
	<div class="items">
		<?php
		if ($cargo == $dbrp->getIDProdutor()) {
		?>
			<a href="/rp/index.php?pg=eventos_produtores">
				<span class="icon"><img src="/temas/rps/imagens/adicionar.svg" /></span>
				<span class="nome">Os meus eventos</span>
			</a>
		<?php
		}
		else if ($cargo == $dbrp->getIDChefeEquipa()) {
			?>
		<a href="/rp/index.php?pg=eventos_equipa">
			<span class="icon"><img src="/temas/rps/imagens/adicionar.svg" /></span>
			<span class="nome">A minha equipa</span>
		</a>
		<?php
		}
		else {
			?>
		<a href="/rp/index.php?pg=eventos_singular">
			<span class="icon"><img src="/temas/rps/imagens/adicionar.svg" /></span>
			<span class="nome">Os meus eventos</span>
		</a>
		<?php
		}
		if ($permissao) {
		?>
		<a href="/rp/index.php?pg=cartoes_sem_consumo">
			<span class="icon"><img src="/temas/rps/imagens/adicionar.svg" /></span>
			<span class="nome">Cartões sem consumo</span>
		</a>
		<?php
		}
		?>

		<a href="/rp/index.php?pg=cartoes_consumo_obrigatorio">
			<span class="icon"><img src="/temas/rps/imagens/adicionar.svg" /></span>
			<span class="nome">Embaixadores</span>
		</a>

		<a href="/rp/index.php?pg=convites">
			<span class="icon"><img src="/temas/rps/imagens/adicionar.svg" /></span>
			<span class="nome">Convites</span>
		</a>
	</div>
</div>

<?php
if ($dbrp->permissaoPrivados() || $permissao || $dbrp->permissaoDisponibilidadeMesas()) {
?>
<div class="tab">
	<h3 class="titulo"> Privados </h3>
	<div class="items">
		<?php
			if ($permissao || $dbrp->permissaoDisponibilidadeMesas()) {
			?>
		<a href="/rp/index.php?pg=disponibilidade_de_mesas">
			<span class="icon"><img src="/temas/rps/imagens/adicionar.svg" /></span>
			<span class="nome">Disponibilidade de mesas</span>
		</a>
		<?php
			}
			if ($permissao) {
			?>
		<a href="/rp/index.php?pg=lista_espera_mesas">
			<span class="icon"><img src="/temas/rps/imagens/adicionar.svg" /></span>
			<span class="nome">Lista de espera de mesas</span>
		</a>
		<?php
			}
			if ($dbrp->permissaoPrivados()) {
			?>
		<a href="/rp/privados/venda.php">
			<span class="icon"><img src="/temas/rps/imagens/adicionar.svg" /></span>
			<span class="nome">Venda de Privados & Garrafas</span>
		</a>
		<?php
			}
			if ($permissao) {
			?>
		<a href="/rp/index.php?pg=reserva_garrafas">
			<span class="icon"><img src="/temas/rps/imagens/adicionar.svg" /></span>
			<span class="nome">Reserva de Garrafas</span>
		</a>
		<?php
			}
			?>
	</div>
</div>
<?php
}
?>

<div class="tab">
	<h3 class="titulo"> Definições de conta </h3>
	<div class="items">
		<a href="/rp/index.php?pg=alterar_password">
			<span class="icon">
				<img src="/temas/rps/imagens/alterar_password.svg" />
			</span>
			<span class="nome">Alterar password</span>
		</a>

		<a href="/rp/logout.php">
			<span class="icon"><img src="/temas/rps/imagens/logout_white.svg" /></span>
			<span class="nome">Logout</span>
		</a>
	</div>
</div>