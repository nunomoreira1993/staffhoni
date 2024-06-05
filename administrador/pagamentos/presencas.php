<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/administrador/index.php');
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$cargos = $dbrps->listaCargosAtrasos();
$letras = $dbrps->listaIniciaisRPs(1, false, $cargos);
?>

<a href="/administrador/index.php?pg=insere_cartao" class="inserir extra">
    Inserir cartão extra
</a>

<div class="content presencas">
    <div class="letras_presencas swiper-container">
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
        include($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/ajax/presencas.ajax.php');
        ?>
    </div>

</div>