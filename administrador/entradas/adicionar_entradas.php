<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/administrador/index.php');
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$letras = $dbrps->listaIniciaisRPs(1);


?>
<div class="content">
    <div class="letras swiper-container">
        <div class="swiper-wrapper">
            <?php
            foreach ($letras as $letra) {
                ?>
                <a href="#" data-letra="<?php echo $letra['letra']; ?>" class="swiper-slide">
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
        include($_SERVER['DOCUMENT_ROOT'] . '/administrador/entradas/ajax/rps.ajax.php');
        ?>
    </div>

</div>