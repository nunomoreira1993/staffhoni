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
    $sala = $dbprivados->devolveSala($_GET['id']);
}
if ($_POST) {
    $campos['activo'] = $_POST['activo'];
    $campos['nome'] = $_POST['nome'];
    if (empty($campos['nome']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo nome.";
    }
    if ($_FILES['foto']['name'] && empty($_SESSION['erro'])) {
        $planta = doUpload($_FILES['foto'], "/privados/", "foto");

        if ($planta) {
            $campos['foto'] = $planta;
        } else {
            $_SESSION['erro'] = $resize['errors']['user'][0];
        }

        if ($_GET['id'] && empty($_SESSION['erro'])) {
            if ($sala['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/privados/" . $sala['foto'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . "/fotos/privados/" . $sala['foto']);
            }
        }
    }
    if (empty($_SESSION['erro'])) {
        if ($_GET['id'] == 0) {
            $db->Insert('privados_salas', $campos);
            $estado = $db->Insert('logs', array('descricao' => "Inseriu uma sala.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção"));
            if ($estado) {
                $_SESSION['sucesso'] = "Inseriu a sala com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao inserir a sala!";
            }
        } else {
            $db->Update('privados_salas', $campos, 'id=' . $_GET['id']);
            $estado = $db->Insert('logs', array('descricao' => "Alterou uma sala.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração"));
            if ($estado) {
                $_SESSION['sucesso'] = "Alterou a sala com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao alterar o sala!";
            }
        }
        header('Location:/administrador/index.php?pg=gestao_salas');
        exit;
    }
}

if ($_GET['id'] == 0) {
    ?>
    <h1 class="titulo"> Inserir - Sala </h1>
<?php
} else {
    ?>
    <h1 class="titulo"> Editar - Sala </h1>
<?php
}
if ($campos) {
    $sala = $campos;
}
?>

<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" name="inserir_rp" id="inserir_rp" enctype="multipart/form-data" autocomplete="off">
        <div class="input-grupo">
            <label for="input-nome">
                Nome
            </label>
            <div class="input">
                <input type="text" value="<?php echo $sala['nome']; ?>" name="nome" id="input-nome-sala" placeholder="Nome da Sala" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Activo
            </label>
            <div class="input">
                <select name="activo">
                    <option value="0" <?php if ($sala['activo'] != 1) { ?> selected="selected" <?php } ?>> Não activo </a>
                    <option value="1" <?php if ($sala['activo'] == 1) { ?> selected="selected" <?php } ?>> Activo </a>
                </select>
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-foto">
                Planta da sala
            </label>
            <div class="input">
                <?php
                if ($sala['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/privados/" . $sala['foto'])) {
                    ?>
                    <div class="foto">
                        <img src="/fotos/privados/<?php echo $sala['foto']; ?>" width="150px">
                    </div>
                <?php
            }
            ?>
                <input type="file" value="<?php echo $sala['foto']; ?>" name="foto" id="input-foto" placeholder="Planta da sala" />
            </div>
        </div>
        <div class="input-grupo">
            <div class="input">
                <input type="submit" value="Enviar" />
            </div>
        </div>

    </form>
</div>