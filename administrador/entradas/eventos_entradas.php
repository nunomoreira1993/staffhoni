<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);


$pagina = intval($_GET['p']);

$quantidade = 10;
$limit = devolveLimit(array('pagina' => $pagina, 'numero' => $quantidade));

if ($_GET['data_evento']) {
    $filtro['data_evento'] = $_GET['data_evento'];
}

$entradasDiasRP = $dbrps->listaEntradasDiasTotal(false, $filtro, $limit);
$numerDiasRP = $dbrps->contaEntradasDiasTotal(false, $filtro);
?>
<h1 class="titulo"> Eventos </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>

    <form class="filtros" name="filtros" action="" method="GET">
        <input type="hidden" name="pg" value="<?php echo $pg; ?>" />
        <input type="hidden" name="p" value="<?php echo $p < 1 ? 1 : $p; ?>" />
        <div class="filtro">
            <span class="nome">Data de Evento:</span>
            <span class="input"><input type="date" name="data_evento" value="<?php echo $filtro['data_evento']; ?>" /></span>
        </div>
        <input type="submit" value="Filtrar" />
        <a href="/administrador/index.php?pg=eventos_entradas&p=<?php echo $_GET['p']; ?>" class="clean"> Limpar filtros </a>
    </form>

    <?php
    echo devolvePaginacao($pagina, $numerDiasRP, $quantidade);
    ?>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Dia do evento</th>
                    <th>Total de entradas</th>
                    <th>Total de Cartões Sem Consumo</th>
                    <th>Total de Embaixadores</th>
                    <th class="text-nowrap"></th>
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
                        <td><?php echo $rpp['total']; ?></td>
                        <td><?php echo $rpp['total_sem_consumo']; ?></td>
                        <td><?php echo $rpp['total_cartoes_consumo_obrigatorio']; ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <?php
                                if ($rpp['fraude'] == 1) {
                                    ?>
                                    <span class="fraude"> Possível Fraude </span>
                                <?php
                            }
                            ?>
                                <a href="/administrador/exportar/exportar_evento.php?data=<?php echo $rpp['data_evento']; ?>" class="exportar-excell"> Exportar para Excell </a>
                                <a href="?pg=entradas_evento_data&data=<?php echo $rpp['data_evento']; ?>" class="entradas"> Ver entradas ao minuto </a>
                                <a href="?pg=entradas_evento_rps&data=<?php echo $rpp['data_evento']; ?>" class="entradas"> Ver entradas por RP </a>
                                <a href="?pg=entradas_evento_produtor&data=<?php echo $rpp['data_evento']; ?>" class="entradas"> Ver entradas por Produtor </a>
                            </div>
                        </td>
                    </tr>
                <?php
            }
            ?>

            </tbody>
        </table>

        <?php
        echo devolvePaginacao($pagina, $numerDiasRP, $quantidade);
        ?>
    </div>
</div>