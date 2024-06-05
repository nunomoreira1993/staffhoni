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
$sala = $dbprivados->devolveSala($_GET['id_sala']);
if ($_GET['id'] > 0) {
    $mesa = $dbprivados->devolveMesa($_GET['id']);
}
if ($_POST) {
    $campos['codigo_mesa'] = $_POST['codigo_mesa'];
    $campos['id_sala'] = $_GET['id_sala'];
    if (empty($campos['codigo_mesa']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo nome.";
    }
    if (empty($_SESSION['erro'])) {
        if ($_GET['id'] == 0) {
            $db->Insert('privados_salas_mesas', $campos);
            $estado = $db->Insert('logs', array('descricao' => "Inseriu uma mesa.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção"));
            if ($estado) {
                $_SESSION['sucesso'] = "Inseriu a mesa com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao inserir a mesa!";
            }
        } else {
            $db->Update('privados_salas_mesas', $campos, 'id=' . $_GET['id']);
            $estado = $db->Insert('logs', array('descricao' => "Alterou uma mesa.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração"));
            if ($estado) {
                $_SESSION['sucesso'] = "Alterou a sala com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao alterar o mesa!";
            }
        }
        header('Location:/administrador/index.php?pg=gestao_mesas&id_sala=' . $_GET['id_sala']);
        exit;
    }
}

if ($_GET['id'] == 0) {
    ?>
<h1 class="titulo"> Inserir - Mesa </h1>
<?php

} else {
    ?>
<h1 class="titulo"> Editar - Mesa </h1>
<?php 
}
if ($campos) {
    $sala = $campos;
}
?>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" name="inserir_mesa" id="inserir_rp" enctype="multipart/form-data" autocomplete="off">
        <div class="input-grupo">
            <label for="input-nome">
                Código de mesa
            </label>
            <div class="input">
                <input type="text" value="<?php echo $mesa['codigo_mesa']; ?>" name="codigo_mesa" id="input-nome-sala" placeholder="Código de mesa" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <div class="input">
                <input type="submit" value="Enviar" />
            </div>
        </div>
    </form>
</div> 