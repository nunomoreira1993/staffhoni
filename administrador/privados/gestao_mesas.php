<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1 && $tipo != 3) {
    header('Location:/administrador/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);
$mesas = $dbprivados->listaMesas($_GET['id_sala']);

$sala = $dbprivados->devolveSala($_GET['id_sala']);
if ($_GET['apagar'] == 1 && $_GET['id'] > 0) {
    $mesa = $dbprivados->devolveMesa($_GET['id']);
    if ($mesa) {
        $query = 'DELETE from privados_salas_mesas WHERE id=' . $_GET['id'];
        $db->query($query);
        $_SESSION['sucesso'] = "A mesa foi apagada.";
        $db->Insert('logs', array('descricao' => "Apagou uma mesa", 'arr' => json_encode($rp), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "apagar"));
        header('Location: /administrador/index.php?pg=gestao_mesas&id_sala=' . $_GET['id_sala']);
        exit;
    }
}

?>
<h1 class="titulo"> Mesas - <?php echo $sala['nome']; ?> <a href="?pg=inserir_mesa&id=0&id_sala=<?php echo $_GET['id_sala']; ?>"> Criar nova mesa </a> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Código de mesa</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                                                                                                if (empty($mesas)) {
                                                                                                    ?>
                                                                                                    <td colspan="5">
                                                                                                        Sem registos inseridos.
                                                                                                    </td>
                                                                                                                                                                                    <?php
                                                                                                }
                                                                                                foreach ($mesas as $mesa) {
                                                                                                    ?>
                                                                                                    <tr>
                                                                                                        <td><?php echo $mesa['codigo_mesa']; ?></td>

                                                                                                    <td class="text-nowrap">
                                                                                                        <div class="opcoes">
                                                                                                            <a href="?pg=inserir_mesa&id=<?php echo $mesa['id']; ?>&id_sala=<?php echo $_GET['id_sala']; ?>" class="editar"> <i class="fa fa-pencil text-inverse m-r-10"></i> </a>
                                                                                                    <a href="?pg=gestao_mesas&apagar=1&id=<?php echo $mesa['id']; ?>&id_sala=<?php echo $_GET['id_sala']; ?>" class="apagar"> <i class="fa fa-close text-danger"></i> </a>
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