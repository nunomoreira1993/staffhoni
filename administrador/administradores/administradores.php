<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1) {
    header('Location:/administrador/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/administradores/administrador.obj.php');
$dbadministrador = new administrador($db);
$administradores = $dbadministrador->listaUtilizadores();
if ($_GET['apagar'] == 1 && $_GET['id'] > 0) {

    $administrador = $dbadministrador->devolveUtilizador($_GET['id']);
    if ($administrador) {
        if ($administrador['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/administradores/" . $administrador['foto'])) {
            var_dump(unlink($_SERVER['DOCUMENT_ROOT'] . "/fotos/administradores/" . $administrador['foto']));
        }
        $query = 'DELETE from administradores WHERE id=' . $_GET['id'];
        $db->query($query);
        $db->Insert('logs', array('descricao' => "Apagou um administrador", 'arr' => json_encode($administrador), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "apagar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        $_SESSION['sucesso'] = "O administrador foi apagado.";
        header('Location: /administrador/index.php?pg=administradores');
        exit;
    }
}

?>
<h1 class="titulo"> Administradores <a href="?pg=inserir_administrador&id=0"> Criar novo </a> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telemóvel</th>
                    <th>Foto</th>
                    <th>Tipo</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($administradores)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            foreach ($administradores as $admin) {
                ?>
                    <tr>
                        <td><?php echo $admin['nome']; ?></td>
                        <td><?php echo $admin['email']; ?></td>
                        <td><?php echo $admin['telemovel']; ?></td>
                        <td>
                            <?php
                            if ($admin['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/administradores/" . $admin['foto'])) {
                                ?>
                                <img src="/fotos/administradores/<?php echo $admin['foto']; ?>" width="60px">
                            <?php

                        }
                        ?>
                        </td>
                        <td><?php if ($admin['tipo'] == 1) { ?> Administrador <?php } else if ($admin['tipo'] == 2) { ?> Staff <?php } else if ($admin['tipo'] == 3) { ?> Privados <?php } else if ($admin['tipo'] == 4) { ?> Caixa <?php } else if ($admin['tipo'] == 5) { ?> Pagamentos <?php }  else if ($admin['tipo'] == 6) { ?> Recepção <?php }  else if ($admin['tipo'] == 7) { ?> Entrada Privados <?php } ?></td>
                        <td class="text-nowrap">
                            <a href="?pg=inserir_administrador&id=<?php echo $admin['id']; ?>" class="editar"> <i class="fa fa-pencil text-inverse m-r-10"></i> </a>
                            <a href="?pg=administradores&apagar=1&id=<?php echo $admin['id']; ?>" class="apagar"> <i class="fa fa-close text-danger"></i> </a>
                        </td>
                    </tr>
                <?php
            }
            ?>

            </tbody>
        </table>
    </div>
</div>