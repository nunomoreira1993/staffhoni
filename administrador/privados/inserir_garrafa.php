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
if ($_GET['id'] > 0) {
    $garrafa = $dbprivados->devolveGarrafa($_GET['id']);
}
if ($_POST) {
    $campos['nome'] = $_POST['nome'];
    $campos['comissao'] = $_POST['comissao'];
    if (empty($campos['nome']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo nome.";
    }
    if (empty($_SESSION['erro'])) {
        if ($_GET['id'] == 0) {
            $db->Insert('garrafas', $campos);
            $estado = $db->Insert('logs', array('descricao' => "Inseriu uma garrafa.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção"));
            if ($estado) {
                $_SESSION['sucesso'] = "Inseriu a garrafa com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao inserir a garrafa!";
            }
        } else {
            $db->Update('garrafas', $campos, 'id=' . $_GET['id']);
            $estado = $db->Insert('logs', array('descricao' => "Alterou uma garrafa.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração"));
            if ($estado) {
                $_SESSION['sucesso'] = "Alterou a garrafa com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao alterar o garrafa!";
            }
        }
        header('Location:/administrador/index.php?pg=gestao_garrafas');
        exit;
    }
}

if ($_GET['id'] == 0) {
    ?>
    <h1 class="titulo"> Inserir - Garrafa </h1>
<?php
} else {
    ?>
    <h1 class="titulo"> Editar - Garrafa </h1>
<?php
}
if ($campos) {
    $garrafa = $campos;
}
?>

<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" name="inserir_rp" id="inserir_rp" enctype="multipart/form-data" autocomplete="off">
        <div class="input-grupo">
            <label for="input-nome">
                Nome
            </label>
            <div class="input">
                <input type="text" value="<?php echo $garrafa['nome']; ?>" name="nome" id="input-nome-garrafa" placeholder="Nome da Garrafa" autocomplete="new-password" />
            </div>
        </div>

        <div class="input-grupo">
            <label for="input-nome">
                Garrafa com comissão:
            </label>
            <div class="input">
                <select name="comissao" id="input-permite_apagar_privados">
                    <option <?php if ($garrafa['comissao'] == 0) { ?> selected="selected" <?php } ?> value="0"> Não </option>

                    <option <?php if ($garrafa['comissao'] == 1) { ?> selected="selected" <?php } ?> value="1"> Sim </option>
                </select>
            </div>
        </div>
        <div class="input-grupo">
            <div class="input">
                <input type="submit" value="Enviar" />
            </div>
        </div>

    </form>
</div>