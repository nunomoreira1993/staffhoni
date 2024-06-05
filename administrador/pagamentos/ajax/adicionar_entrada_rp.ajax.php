<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$id_rp = $_GET['id'];
if ($id_rp) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
    $dbrps = new rps($db);
    $rp = $dbrps->devolveRP($id_rp); 
    ?>
    <div class="entrada-ajax">
        <div class="rp" data-id-rp="<?php echo $id_rp; ?>">
            <?php 
            if($rp['foto'] && file_exists($_SERVER['DOCUMENT_ROOT']."/fotos/rps/". $rp['foto'])){
            ?>
                <span class="foto">
                    <img src="/fotos/rps/<?php echo $rp['foto']; ?>" />
                </span>
                <?php 
            }
            ?>
            <span class="nome">
                <?php echo $rp['nome']; ?>
            </span>
        </div>
        <div class="resultado"></div>
        <div class="calculadora">
            <?php 
            for($i=1; $i<=9; $i++){
                ?>
                <a href="#" data-numero="<?php echo $i; ?>" class="numero">
                    <?php echo $i; ?>
                </a>
                <?php 
            }
            ?>
            <a href="#" data-numero="0" class="numero">
                0
            </a>
            <a href="#" data-numero="delete" class="apagar">
                🠐
            </a>
            <a href="#" data-numero="ok" class="ok">
                Gravar
            </a>

        </div>
        <div class="sucesso">
            <h2> Inserido com sucesso! </h2>
            <div class="icon">
                <svg width="150" height="150" viewBox="0 0 510 510" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <path fill="#fff" d="M150.45,206.55l-35.7,35.7L229.5,357l255-255l-35.7-35.7L229.5,285.6L150.45,206.55z M459,255c0,112.2-91.8,204-204,204 S51,367.2,51,255S142.8,51,255,51c20.4,0,38.25,2.55,56.1,7.65l40.801-40.8C321.3,7.65,288.15,0,255,0C114.75,0,0,114.75,0,255 s114.75,255,255,255s255-114.75,255-255H459z"></path>
                </svg>
            </div>
            
            <a href="#" class="fechar">
                Fechar
            </a>
        </div>
    </div>
<?php
}
?>