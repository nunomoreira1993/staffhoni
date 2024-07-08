<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);
$vendasDias = $dbprivados->listaVendasDiasTotal();
?>
<h1 class="titulo"> Vendas por evento </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Dia do evento</th>
                    <th>Total de vendas</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($vendasDias)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            foreach ($vendasDias as $venda) {
                ?>
                    <tr>
                        <td><?php echo $venda['data_evento']; ?></td>
                        <td><?php echo $venda['total']; ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="/administrador/exportar/exportar_privados_garrafas.php?data=<?php echo $venda['data_evento']; ?>" class="exportar-excell"> Exportar Garrafas </a>
                                <a href="/administrador/exportar/exportar_privados.php?data=<?php echo $venda['data_evento']; ?>" class="exportar-excell"> Exportar para Excell </a>
                                <a href="?pg=privados_evento_data&data=<?php echo $venda['data_evento']; ?>" class="entradas"> Ver vendas evento </a>
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