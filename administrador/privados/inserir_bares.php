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
    $bar = $dbprivados->devolveBar($_GET['id']);
}
if ($_POST) {
    $campos['nome'] = $_POST['nome'];
    if (empty($campos['nome']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo nome.";
    }
    if (empty($_SESSION['erro'])) {
        if ($_GET['id'] == 0) {
            $db->Insert('bares', $campos);
            $estado = $db->Insert('logs', array('descricao' => "Inseriu um bar.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção"));
            if ($estado) {
                $_SESSION['sucesso'] = "Inseriu a bar com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao inserir a bar!";
            }
        } else {
            $db->Update('bares', $campos, 'id=' . $_GET['id']);
            $estado = $db->Insert('logs', array('descricao' => "Alterou um bar.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração"));
            if ($estado) {
                $_SESSION['sucesso'] = "Alterou a bar com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao alterar o bar!";
            }
        }
        header('Location:/administrador/index.php?pg=gestao_bares');
        exit;
    }
}

if ($_GET['id'] == 0) {
    ?>
    <h1 class="titulo"> Inserir - bar </h1>
<?php
} else {
    ?>
    <h1 class="titulo"> Editar - bar </h1>
<?php
}

if ($campos) {
    $bar = $campos;
}
?>

<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" name="inserir_bar" id="inserir_bar" enctype="multipart/form-data" autocomplete="off">
        <div class="input-grupo">
            <label for="input-nome">
                Nome
            </label>
            <div class="input">
                <input type="text" value="<?php echo $bar['nome']; ?>" name="nome" id="input-nome-bar" placeholder="Nome do bar" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <div class="input">
                <input type="submit" value="Enviar" />
            </div>
        </div>

    </form>
</div>