<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$data = $_GET['data'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$entradasRPData = $dbrps->listaEntradasEquipasDia($data, $_GET['id_produtor']);

$rp = $dbrps->devolveRP($_GET['id_produtor']);
?>
<h1 class="titulo"> Lista de entradas por equipa do produtor "<?php echo $rp['nome']; ?>"  do evento do dia <?php echo $data; ?></h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Nome do Produtor</th>
                    <th>Nº de Entradas</th>
                    <th>Total Privados (€) </th>
                    <th>Total Garrafas (€) </th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($entradasRPData)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            foreach ($entradasRPData as $rpp) {
                ?>
                    <tr>
                        <td><?php echo $rpp['nome']; ?></td>
                        <td><?php echo $rpp['total']; ?></td>
                        <td><?php echo euro($rpp['total_privados']); ?></td>
                        <td><?php echo euro($rpp['total_garrafas']); ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="?pg=entradas_evento_rps&id_chefe_equipa=<?php echo $rpp['id']; ?>&data=<?php echo $data; ?>" class="entradas"> Ver entradas por RP </a>
                            </div>
                        </td>

                    </tr>
                <?php
            }
            ?>

            </tbody>
        </table>
    </div>
</div>