<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1) {
    header('Location:/administrador/index.php');
    exit;
}
$id = $_GET['id'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$entradasDiasRP = $dbrps->listaEntradasDiasRP($id);

$rp = $dbrps->devolveRP($id);

?>
<h1 class="titulo"> Entradas Staff - <?php echo $rp['nome']; ?> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Dia do evento</th>
                    <th>Quantidade</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (empty($entradasDiasRP)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                    <?php

                }
                foreach ($entradasDiasRP as $rpp) {
                    ?>
                    <tr>
                        <td><?php echo $rpp['data_evento']; ?></td>
                        <td><?php echo $rpp['quantidade']; ?></td>
                    </tr>
                    <?php 
                }
                ?>
               
            </tbody>
        </table>
    </div>
</div>