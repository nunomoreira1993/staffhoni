<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$data = $_GET['data'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$entradasRPData = $dbrps->listaEntradasProdutoresDia($data);
?>
<h1 class="titulo"> Lista de entradas por Produtor do evento do dia <?php echo $data; ?></h1>
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
                                <a href="?pg=entradas_evento_equipa&id_produtor=<?php echo $rpp['id']; ?>&data=<?php echo $data; ?>" class="entradas"> Ver entradas por equipa </a>
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