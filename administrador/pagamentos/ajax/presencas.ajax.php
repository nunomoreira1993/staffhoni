<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$letra = $_GET['letra'];
$cargos = $dbrps->listaCargosAtrasos();
$rps = $dbrps->listaRPs($letra, 1, false, $cargos);
foreach ($rps as $rp) {
    ?>
    <a href="javascript:;" data-id="<?php echo $rp['id']; ?>">
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
            <span class="cargo" style="font-weight:400; font-size:14px; width:100%;
				            display:inline-block; padding-top:6px;">
                <?php
                echo $rp['cargo'];
                ?>
            </span>
        </span>
        <?php
        if ($dbrps->verificaPresencaRP($rp['id']) == 1) {
            ?>
            <span class="presenca_confirmada"> Entrada confirmada </span>
        <?php
    } else {
        ?>
            <span class="confirmar_presenca"> Confirmar entrada </span>
        <?php

    }
    ?>
    </a>
<?php
}
?>