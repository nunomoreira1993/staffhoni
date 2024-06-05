<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);

$pagina = intval($_GET['p']);
$quantidade =10;
$limit = devolveLimit(array('pagina' => $pagina, 'numero' => $quantidade));

$presencas = $dbpagamentos->listaPresencasEventos($filtros, $limit);
$numero = $dbpagamentos->contaPresencasEventos($filtros);
?>
<h1 class="titulo"> Relatório - Entradas </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>

    <?php
    echo devolvePaginacao($pagina, $numero, $quantidade);
    ?>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Data do Evento</th>
                    <th>Total </th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($presencas)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            foreach ($presencas as $presenca) {
                ?>
                    <tr>
                        <td><?php echo $presenca['data_evento']; ?></td>
                        <td><?php echo $presenca['conta']; ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="?pg=ver_entradas&data=<?php echo $presenca['data_evento']; ?>" class="entradas"> Ver entradas Staffs </a>
                            </div>
                        </td>
                    </tr>
            <?php
            }
            ?>

            </tbody>
        </table>
    </div>
    <?php
    echo devolvePaginacao($pagina, $numero, $quantidade);
    ?>
</div>