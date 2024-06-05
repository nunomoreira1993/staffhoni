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
$garrafas = $dbprivados->listaGarrafas();

if ($_GET['apagar'] == 1 && $_GET['id'] > 0) {
    $garrafa = $dbprivados->devolveGarrafa($_GET['id']);
    if ($garrafa) {
        $query = 'DELETE from garrafas WHERE id=' . $_GET['id'];
        $db->query($query);
        $_SESSION['sucesso'] = "A garrafa foi apagada.";
        $db->Insert('logs', array('descricao' => "Apagou uma garrafa", 'arr' => json_encode($rp), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "apagar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        header('Location: /administrador/index.php?pg=gestao_garrafas');
        exit;
    }
}

?>
<h1 class="titulo"> Garrafas <a href="?pg=inserir_garrafa&id=0"> Criar nova garrafa </a> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Nome da garrafa</th>
                    <th>Comissão</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($garrafas)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php
                }
                foreach ($garrafas as $garrafa) {
                    ?>
                    <tr>
                        <td><?php echo $garrafa['nome']; ?></td>
                        <td><?php echo $garrafa['comissao']?"Sim":"Não"; ?></td>
                        </td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="?pg=inserir_garrafa&id=<?php echo $garrafa['id']; ?>" class="editar"> <i class="fa fa-pencil text-inverse m-r-10"></i> </a>
                                <a href="?pg=gestao_garrafas&apagar=1&id=<?php echo $garrafa['id']; ?>" class="apagar"> <i class="fa fa-close text-danger"></i> </a>
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