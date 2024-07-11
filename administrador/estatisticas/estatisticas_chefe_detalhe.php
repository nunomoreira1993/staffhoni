<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

$pagina = intval($_GET['p']) ?: 1;

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/estatisticas/estatisticas.obj.php');
$dbestatisticas = new estatisticas($db);

$quantidade = 20;
$limit = devolveLimit(array('pagina' => $pagina, 'numero' => $quantidade));

$dates = $dbestatisticas->getStatisticByEntranceTeamDay($_GET["data_evento"], $limit);
?>
<h1 class="titulo"> Estatísticas Chefe de Equipa (Entradas Equipa) - Data de Evento <?php echo $dates["result"][0]["semana"]; ?> </h1>
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
                    <th>Posição</th>
                    <th>Nome</th>
                    <th>Total Entradas</th>
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
                        <td><?php echo $result['posicao']; ?></td>
                        <td><?php echo $result['nome']; ?></td>
                        <td><?php echo $result['entradas']; ?></td>
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