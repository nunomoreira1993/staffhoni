<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$gerente = (int)$_GET['gerente'];


require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$letras = $dbrps->listaIniciaisRPs(1, $gerente);
?>

<div class="content presencas">
    <div class="letras swiper-container">
        <div class="swiper-wrapper">
            <?php
            foreach ($letras as $letra) {
                ?>
                <a href="#" data-letra="<?php echo $letra['letra']; ?>" data-gerente="<?php echo $gerente; ?>" class="swiper-slide">
                    <?php
                    print $letra['letra'];
                    ?>
                </a>
            <?php
        }
        ?>
        </div>
    </div>
    <div class="rps">
        <?php
        include($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/ajax/adicionar_rps.ajax.php');
        ?>
    </div>
</div>