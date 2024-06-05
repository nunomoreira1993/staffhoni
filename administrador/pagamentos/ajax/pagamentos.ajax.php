<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$letra = $_GET['letra'];
$rps = $dbrps->listaRPs($letra, 1);

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);

foreach ($rps as $rp) {
    $datas_pagamento = $dbpagamentos->devolveDatasParaPagamento($rp['id']);
    $total = $dbpagamentos->devolvePagamento($rp['id'])['total'];

    if (empty($conta_corrente)) {
        $url = "?pg=insere_pagamentos&id_rp=" . $rp['id'];
    } else {
        $url = "javascript:;";
    }
?>
    <a href="<?php echo $url; ?>" data-id="<?php echo $rp['id']; ?>">
        <?php
        if ($rp['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/rps/" . $rp['foto'])) {
        ?>
            <span class="foto">
                <img src="/fotos/rps/<?php echo $rp['foto']; ?>" />
            </span>
        <?php
        }
        ?>
        <span class="nome">
            <?php
            echo $rp['nome'];
            ?>
            <span class="cargo" style="font-weight:400; font-size:14px; width:100%; display:inline-block; padding-top:6px;">
                <?php
                echo $rp['cargo'];
                ?>
            </span>
        </span>
        <?php
        if (empty($datas_pagamento) || $total == 0) {
        ?>
            <span class="pago"> Situação regularizada </span>
        <?php
        } else {
        ?>
            <span class="efectuar_pagamento"> Efectuar pagamento </span>
        <?php

        }
        ?>
    </a>
<?php
}
?>