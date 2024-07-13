<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);
$rp = $dbrp->devolveInfo();
if(empty($rp["qrcode"])){
	$qrcode_hash = md5(strtotime("now") . $rp["id"]);
	$db->update("rps", array("qrcode"=> $qrcode_hash), "id = " . $rp["id"]);
	include_once($_SERVER["DOCUMENT_ROOT"] . '/plugins/phpqrcode/lib/full/qrlib.php');
	$qrcode =  "/fotos/convites/" . "honi_qrcode_" . $rp["id"].".png";
	QRcode::png($qrcode_hash, $_SERVER["DOCUMENT_ROOT"] . $qrcode, QR_ECLEVEL_L, 35, 2);
	$rp = $dbrp->devolveInfo();
}
else {
	$qrcode =  "/fotos/convites/" . "honi_qrcode_" . $rp["id"].".png";
	if(!file_exists($_SERVER["DOCUMENT_ROOT"] . $qrcode)) {
		include_once($_SERVER["DOCUMENT_ROOT"] . '/plugins/phpqrcode/lib/full/qrlib.php');
		$qrcode =  "/fotos/convites/" . "honi_qrcode_" . $rp["id"].".png";
		QRcode::png($rp["qrcode"], $_SERVER["DOCUMENT_ROOT"] . $qrcode, QR_ECLEVEL_L, 35, 2);
	}
}
$eventos = $dbrp->listaEventosRP();

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
$pagamentos = $dbpagamentos->devolvePagamento($_SESSION['id_rp']);
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
	<div class="qrcode-rp-homepage">
		<a href="/download_qrcode.php?rp=1&id=<?php echo $_SESSION['id_rp']; ?>" class="download">
			<span class="image"><img src="<?php echo $qrcode; ?>" /></span>
			<span class="link">Download QR Code</span>
		</a>
	</div>
	<div class="conta-corrente-homepage">
		<div class="saldo">
			<span class="titulo">
				Saldo
			</span>
			<span class="valor <?php if ($pagamentos['total'] < 0) { ?> pago <?php } else if ($pagamentos['total'] > 0) { ?> recebido <?php } ?>">
				<?php
				echo euro($pagamentos['total']);
				?>
			</span>
		</div>
		<a href="?pg=historico_pagamentos"> Ver histórico de pagamentos </a>
	</div>
</div>
<div class="entradas">
	<?php
	unset($pagamentos["total"]);
	if ($pagamentos) {
		foreach ($pagamentos as $key_pagamento => $pagamento) {
			if($key_pagamento == "total") {
				continue;
			}
			if ($key_pagamento == "guest") {
                $nome_pagamento = "Comissão Guest";

                if($pagamento["comissao_bonus"] > 0){
                    unset($campo);
                    $nome_pagamento = "Comissão Guest";
                }
            }
            if ($key_pagamento == "guest_team") {
                $nome_pagamento = "Comissão Guest - Equipa";

                if($pagamento["comissao_bonus"] > 0){
                    $nome_pagamento = "Comissão Guest - Equipa";
                }
            }
            if ($key_pagamento == "privados") {
                $nome_pagamento = "Privados";
            }
            if ($key_pagamento == "privados_chefe") {
                $nome_pagamento = "Privados Equipa";
            }
            if ($key_pagamento == "garrafas") {
                $nome_pagamento = "Garrafas Bar";
            }
            if ($key_pagamento == "sessoes") {
                $nome_pagamento = "Sessão";
            }
            if ($key_pagamento == "estatisticas_chefe") {
                $nome_pagamento = "Prémio Melhor Chefe de Equipa";
            }
            if ($key_pagamento == "estatisticas_rp") {
                $nome_pagamento = "Prémio Melhor RP";
            }
            if ($key_pagamento == "estatisticas_privados") {
                $nome_pagamento = "Prémio Mais Vendas Privados";
            }
            if ($key_pagamento == "atrasos") {
                $nome_pagamento = "Atraso";
            }
            if ($key_pagamento == "convites") {
                $nome_pagamento = "Penalização convite";
            }

            if ($key_pagamento == "divida") {
                $nome_pagamento = "Dívida";
            }
			if ($key_pagamento == 'extras') {
				$nome_pagamento = "Extras";
			}
			?>
			<div class="evento">
				<div class="topo">
					<span class="data">
						<?php echo $nome_pagamento; ?>
					</span>
					<span class="estado">
					</span>
				</div>
				<div class="rodape">
					<?php
					if($pagamento["comissao"] > 0) {
						?>
						<span class="item">
							<span class="titulo">
								<?php echo $pagamento["descricao"]; ?>
							</span>
							<span class="valor">
								<?php echo euro($pagamento["comissao"]); ?>
							</span>
						</span>
						<?php
					}
					if($pagamento["comissao_bonus"] > 0 ){
						?>
						<span class="item">
							<span class="titulo">
								<?php echo $pagamento["descricao_bonus"]; ?>
							</span>
							<span class="valor">
								<?php echo euro($pagamento["comissao_bonus"]); ?>
							</span>
						</span>
						<?php
					}
					if($pagamento['items'])	{
						foreach ($pagamento['items'] as $items) {
							?>
							<span class="item">
								<span class="titulo">
									<?php echo $items["descricao"]; ?>
								</span>
								<span class="valor">
									<?php echo euro($items["valor"]); ?>
								</span>
							</span>
							<?php
						}
					}
					?>
				</div>
			</div>
		<?php
	}
} else {
	?>
		<div class="sem_registos">
			Sem pagamentos a regularizar.
		</div>
	<?php
}
?>
</div>