<?php

include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador']) && empty($_SESSION['id_rp'])) {
    header('Location:/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);
$garrafas = $dbprivados->listaGarrafas();

?>
<h1 class="titulo"> Garrafas <a href="?pg=inserir_garrafa&id=0"> Criar nova garrafa </a> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="escolha-garrafas-responsive">
        <?php
        foreach ($garrafas as $garrafa) {
            ?>
        <div class="garrafa-div">
            <div class="nome">
                <?php echo $garrafa['nome']; ?>
            </div>

            <div class="input-quantidade">
                <a href="#" class="menos" data-id="<?php echo $garrafa['id']; ?>"><img src="/temas/administrador/imagens/menos.svg" /></a>
                <input type="number" data-idgarrafa="<?php echo $garrafa['id']; ?>" id="input-<?php echo $garrafa['id']; ?>" value="0" name="garrafas[<?php echo $garrafa['id']; ?>]" />
                <a href="#" class="mais" data-id="<?php echo $garrafa['id']; ?>"><img src="/temas/administrador/imagens/mais.svg" /></a>
            </div>
        </div>
        <?php 
    }
    ?>
    </div>
    <div class="acao">
        <a href="#" class="gravar"> Gravar </a>
    </div>
</div> 