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
$salas = $dbprivados->listaSalas();

if ($_GET['apagar'] == 1 && $_GET['id'] > 0) {
    $sala = $dbprivados->devolveSala($_GET['id']);
    if ($sala) {
        if ($sala['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/privados/" . $sala['foto'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . "/fotos/privados/" . $sala['foto']);
        }
        $query = 'DELETE from privados_salas WHERE id=' . $_GET['id'];
        $db->query($query);
        $_SESSION['sucesso'] = "A sala foi apagada.";
        $db->Insert('logs', array('descricao' => "Apagou uma sala", 'arr' => json_encode($rp), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "apagar"));
        header('Location: /administrador/index.php?pg=gestao_salas');
        exit;
    }
}

?>
<h1 class="titulo"> Salas <a href="?pg=inserir_sala&id=0"> Criar nova sala </a> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Nome da Sala</th>
                    <th>Planta de mesas</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                        if (empty($salas)) {
                            ?>
                            <td colspan="5">
                                Sem registos inseridos.
                            </td>
                                    <?php
                        }
                        foreach ($salas as $sala) {
                            ?>
                            <tr>
                                <td><?php echo $sala['nome']; ?></td>
                            <td>

                                <?php
                            if ($sala['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/privados/" . $sala['foto'])) {
                                ?>
                                <img src="/fotos/privados/<?php echo $sala['foto']; ?>" height="60px">
                                                                                    <?php
                                                }
                                                ?>
                                                </td>

                                                <td class="text-nowrap">
                                                    <div class="opcoes">
                                                        <a href="?pg=gestao_mesas&id_sala=<?php echo $sala['id']; ?>" class="entradas"> Ver mesas </a>
                                                                <a href="?pg=inserir_sala&id=<?php echo $sala['id']; ?>" class="editar"> <i class="fa fa-pencil text-inverse m-r-10"></i> </a>
                                                                <a href="?pg=gestao_salas&apagar=1&id=<?php echo $sala['id']; ?>" class="apagar"> <i class="fa fa-close text-danger"></i> </a>
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