<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/administrador/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$letras = $dbrps->listaIniciaisRPs(1);


?>
<div class="content pagamentos" <?php echo escreveErroSucesso(); ?>>


    <a href="/administrador/index.php?pg=insere_pagamentos" class="inserir">
        Inserir pagamento extra
    </a>

    <a href="/administrador/index.php?pg=insere_caixa" class="inserir">
        Entradas de caixa
    </a>

    <div class="letras_pagamentos swiper-container">
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
        include($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/ajax/pagamentos.ajax.php');
        ?>
    </div>

</div>