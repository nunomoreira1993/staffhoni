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

$dates = $dbestatisticas->getDaysStatisticByEntranceTeamDay($limit);
?>
<h1 class="titulo"> Estatísticas Chefe de Equipa (Entradas Equipa) </h1>
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
                    <th>Data do Evento</th>
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
                        <td><?php echo $result['data_evento']; ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="?pg=estatisticas_chefe_detalhe&data_evento=<?php echo $result['data_evento']; ?>" class="entradas"> Ver pontuação </a>
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