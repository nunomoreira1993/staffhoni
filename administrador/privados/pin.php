<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$rps = $dbrps->listaRPs(false, false, false, array(1, 5));
?>
<div class="login-pin">
    <div class="lista">
        <?php 
        foreach($rps as $rp){
            ?>
            <a class="rp" href="#" data-id="<?php echo $rp['id']; ?>">
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
                    <span class="cargo">
                        <?php 
                        echo $rp['cargo'];
                        ?>
                    </span>
                </span>
            </a>
            <?php
        }
        ?>
    </div>
    <div class="fundo-formulario">
        <form class="formulario" name="formulario" action="" method="post">
            <button type="button" data-fancybox-close="" class="fechar" title="Close"><svg xmlns="http://www.w3.org/2000/svg" version="1" viewBox="0 0 24 24"><path d="M13 12l5-5-1-1-5 5-5-5-1 1 5 5-5 5 1 1 5-5 5 5 1-1z"></path></svg></button>
            <input type="hidden" name="id_rp" value="" />
            <div class="info">
        
            </div>
            <span class="mensagem"> Introduz um pin com 4 digitos. </span>
            <span class="erro"> Pin inválido. Tente novamente. </span>
            <div class="input">
                <input type="password" value="" name="pin" maxlength="4" />
            </div>
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
                    Entrar
                </a>
            </div>
        </form>
    </div>
</div>

