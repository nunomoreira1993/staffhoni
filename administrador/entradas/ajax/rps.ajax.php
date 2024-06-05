<?php 
    include_once $_SERVER['DOCUMENT_ROOT']."/lib/config.php";
    if (empty($_SESSION['id_utilizador'])) {
        header('Location:/index.php');
        exit;
    }
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
    $dbrps = new rps($db);
    $letra = $_GET['letra'];
    $rps = $dbrps->listaRPs($letra, 1);
    foreach ($rps as $rp) {
        ?>
        <a data-fancybox data-type="ajax" data-src="/administrador/entradas/ajax/adicionar_entrada_rp.ajax.php?id=<?php echo $rp['id']; ?>" href="javascript:;">
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
				<span class="cargo" style="font-weight:400; font-size:14px; width:100%;
				display:inline-block; padding-top:6px;">
					<?php 
					echo $rp['cargo'];
					?>
				</span>
            </span>
            <span class="entradas">
                <span class="valor">
                    <?php echo $rp['entradas']; ?>
                </span>
                <span class="label">
                    Entradas no evento
                </span>
            </span>
        </a>
        <?php 
    }
    ?>