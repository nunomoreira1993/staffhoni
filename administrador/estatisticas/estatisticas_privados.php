<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

$pagina = intval($_GET['p']);

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/estatisticas/estatisticas.obj.php');
$dbestatisticas = new estatisticas($db);

$quantidade = 20;
$limit = devolveLimit(array('pagina' => $pagina, 'numero' => $quantidade));

$dates = $dbestatisticas->getDaysStatisticByPrivadosRPWeekly($limit);
?>
<h1 class="titulo"> Estatísticas Venda de privados </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>

    <div style="margin-bottom:20px" class="filtros">
        <span class="registos"> Foram encontrados <b> <?php echo $dates["count"]; ?> </b> registos. </span>
    </div>

    <?php
    echo devolvePaginacao($pagina, $dates["count"], $quantidade);
    ?>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Semana Nº</th>
                    <th>Data Inicial da Semana</th>
                    <th>Data Final da Semana</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($dates["result"])) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            foreach ($dates["result"] as $result) {
                ?>
                    <tr>
                        <td><?php echo $result['semana']; ?></td>
                        <td><?php echo $result['semana_de']; ?></td>
                        <td><?php echo $result['semana_ate']; ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="?pg=estatisticas_privados_detalhe&semana=<?php echo $result['semana']; ?>" class="entradas"> Ver pontuação </a>
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
    echo devolvePaginacao($pagina, $numeroRps, $quantidade);
    ?>
</div>