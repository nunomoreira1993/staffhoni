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
$bares = $dbprivados->listaBares();

if ($_GET['apagar'] == 1 && $_GET['id'] > 0) {
    $bar = $dbprivados->devolveBar($_GET['id']);
    if ($bar) {
        $query = 'DELETE from bares WHERE id=' . $_GET['id'];
        $db->query($query);
        $_SESSION['sucesso'] = "A bar foi apagada.";
        $db->Insert('logs', array('descricao' => "Apagou um bar", 'arr' => json_encode($rp), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "apagar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        header('Location: /administrador/index.php?pg=gestao_bares');
        exit;
    }
}

?>
<h1 class="titulo"> Bar <a href="?pg=inserir_bares&id=0"> Criar novo bar </a> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Nome do bar</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($bares)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php
            }
            foreach ($bares as $bar) {
                ?>
                    <tr>
                        <td><?php echo $bar['nome']; ?></td>
                        </td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="?pg=inserir_bar&id=<?php echo $bar['id']; ?>" class="editar"> <i class="fa fa-pencil text-inverse m-r-10"></i> </a>
                                <a href="?pg=gestao_bares&apagar=1&id=<?php echo $bar['id']; ?>" class="apagar"> <i class="fa fa-close text-danger"></i> </a>
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