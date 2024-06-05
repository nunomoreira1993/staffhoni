<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$id = (int)$_GET['id'];
if ($id) {
    $rp = $dbrps->devolveRP($id);

    if ($rp['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/rps/" . $rp['foto'])) {
        ?>
    <span class="foto">
        <img src="/fotos/rps/<?php echo $rp['foto']; ?>" />
    </span>
    <?php
        } ?>
    <span class="nome">
        <?php
        echo $rp['nome']; ?>
    </span> 
<?php
}
?>