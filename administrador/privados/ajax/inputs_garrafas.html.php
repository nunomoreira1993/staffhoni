<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if($_POST['garrafas']){
    $garrafas_escolhidas = array_filter($_POST['garrafas']);
    $ids = array_keys($garrafas_escolhidas);
}


if($ids){
    require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
    $dbprivados = new privados($db);
    $garrafas = $dbprivados->listaGarrafas($ids);
    if ($garrafas) {
        foreach ($garrafas as $garrafa) {
            ?>
            <div class="garrafa-div">
                <div class="nome">
                    <?php echo $garrafa['nome']; ?>
                </div>

                <div class="input-quantidade">
                    <a href="#" class="menos" data-id="<?php echo $garrafa['id']; ?>"><img src="/temas/administrador/imagens/menos.svg" /></a>
                    <input type="number" data-idgarrafa="<?php echo $garrafa['id']; ?>" id="input-<?php echo $garrafa['id']; ?>" value="<?php echo $garrafas_escolhidas[$garrafa['id']]; ?>" name="garrafas[<?php echo $garrafa['id']; ?>]" />
                    <a href="#" class="mais" data-id="<?php echo $garrafa['id']; ?>"><img src="/temas/administrador/imagens/mais.svg" /></a>
                </div>
            </div>
            <?php
        }
    }
}
