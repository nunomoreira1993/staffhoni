<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);

$pagina = intval($_GET['p']);
$quantidade = 10;
$limit = devolveLimit(array('pagina' => $pagina, 'numero' => $quantidade));

$convites = $dbpagamentos->listaConvitesEventos($filtros, $limit);
$numeroConvites  = $dbpagamentos->contaConvitesEventos($filtros);
?>
<h1 class="titulo"> Convites </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>

    <?php
    echo devolvePaginacao($pagina, $numeroConvites, $quantidade);
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
                if (empty($convites)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            foreach ($convites as $convite) {
                ?>
                    <tr>
                        <td><?php echo $convite['data_evento']; ?></td>
                        <td><?php echo $convite['conta']; ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="?pg=ver_convites&data=<?php echo $convite['data_evento']; ?>" class="entradas"> Ver convites </a>
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
    echo devolvePaginacao($pagina, $numeroConvites, $quantidade);
    ?>
</div>