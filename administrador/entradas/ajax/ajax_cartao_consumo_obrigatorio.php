<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
$pesquisa = $_POST['pesquisa'];
$id_rp = $_POST['id_rp'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$cartoes = $dbrps->devolveCartoesConsumoObrigatorio($pesquisa, $id_rp);
$string_pesquisa = "";
if ($pesquisa) {
    $string_pesquisa .= 'com o nome de cliente "' . $pesquisa . '"';
}
if ($id_rp) {
    $rp_pesquisa = $dbrps->devolveRP($id_rp);
    if($string_pesquisa != ""){
        $string_pesquisa .= " e ";    
    }
    $string_pesquisa .= 'com o nome do STAFF de "' . $rp_pesquisa['nome'] . '"';
}
?>
<div style="margin-bottom:20px" class="filtros">
    <span class="registos"> Foram encontrados <b> <?php echo count($cartoes); ?> </b> registos <?php echo $string_pesquisa; ?> </span>
</div>
<div class="table-responsive">
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Nome do Staff</th>
                <th>Foto</th>
                <th class="text-nowrap"></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (empty($cartoes)) {
                ?>
                <td colspan="5">
                    Sem registos inseridos.
                </td>
                <?php

            }
            foreach ($cartoes as $cartao) {
                ?>
                <tr>
                    <td><?php echo $cartao['nome']; ?></td>
                    <td><?php echo $cartao['nome_rp']; ?></td>
                    <td>
                        <?php 
                        if ($cartao['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/rps/" . $cartao['foto'])) {
                            ?>
                                <img src="/fotos/rps/<?php echo $cartao['foto']; ?>" width="150px">
                            <?php

                        }
                        ?>
                        
                    </td>
                    <td class="text-nowrap">
                        <div class="opcoes">
                            <a href="#" data-id="<?php echo $cartao['id']; ?>" class="confirmar <?php if($cartao['entrou'] == 1){ ?> esconde <?php }?>"> Confirmar </a>
                            <a href="#" data-id="<?php echo $cartao['id']; ?>" class="anular <?php if($cartao['entrou'] == 0){ ?> esconde <?php }?>"> Anular </a>
                        </div>
                    </td>
                </tr>
                <?php 
            }
            ?>
        
        </tbody>
    </table>
</div>
