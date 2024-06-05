<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$rpscartoes = $dbrps->RPSCartoesConsumoObrigatorioData();

?>
<h1 class="titulo"> Embaixadores</h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="pesquisa">
        <div class="input">
            <label> Pesquisa por Nome do Cliente </label>
            <input type="text" name="pesquisa" data-pesquisa="consumo-obrigatorio" />
        </div>
        <div class="input">
            <label> Pesquisa por Staff </label>
            <select name="rp">
                <option value="">  Escolher um RP para pesquisar </option>
                <?php
                foreach($rpscartoes as $rps){
                    ?>
                    <option value="<?php echo $rps['id']; ?>"> <?php echo $rps['nome']; ?> </option>
                    <?php
                }
                ?>
            </select>
        </div>
        <input type="submit" value="Pesquisar" />
    </div>
    <div class="ajax">

        <?php
        // include $_SERVER['DOCUMENT_ROOT']."/administrador/ajax_cartao_consumo_obrigatorio.php";
        ?>
    </div>
</div>