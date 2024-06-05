<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1) {
    header('Location:/administrador/index.php');
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
if ($_GET['id'] > 0) {
    $logica = $dbpagamentos->devolveLogicaAtrasos($_GET['id']);
}
if ($_POST) {
    $campos['de'] = $_POST['de'];
    $campos['ate'] = $_POST['ate'];
    $campos['valor'] = $_POST['valor'];
    if (empty($campos['de']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo De.";
    }
    if (empty($campos['ate']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo Até.";
    }
    if (empty($_SESSION['erro'])) {
        if ($_GET['id'] == 0) {
            $db->Insert('logica_atrasos', $campos);
            $estado = $db->Insert('logs', array('descricao' => "Inseriu um registo de lógica de atrasos.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção"));
            if ($estado) {
                $_SESSION['sucesso'] = "Inseriu um registo de lógica de atrasos com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao inserir um registo de lógica de atrasos!";
            }
        } else {
            $db->Update('logica_atrasos', $campos, 'id=' . $_GET['id']);
            $estado = $db->Insert('logs', array('descricao' => "Alterou um registo de lógica de atrasos.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração"));
            if ($estado) {
                $_SESSION['sucesso'] = "Alterou um registo de lógica de atrasos com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao alterar registo de lógica de atrasos!";
            }
        }
        header('Location:/administrador/index.php?pg=logica_atrasos');
        exit;
    }
}

if ($_GET['id'] == 0) {
    ?>
    <h1 class="titulo"> Inserir - Linha de lógica de atrasos </h1>
<?php
} else {
    ?>
    <h1 class="titulo"> Editar - Linha de lógica de atrasos </h1>
<?php
}
if ($campos) {
    $logica = $campos;
}
?>

<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" name="inserir_rp" id="inserir_rp" enctype="multipart/form-data" autocomplete="off">
        <div class="input-grupo">
            <label for="input-nome">
                Hora de:
            </label>
            <div class="input">
                <input type="time" value="<?php echo $logica['de']; ?>" name="de" id="input-de" placeholder="Hora de" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Hora até:
            </label>
            <div class="input">
                <input type="time" value="<?php echo $logica['ate']; ?>" name="ate" id="input-ate" placeholder="Hora até" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Valor(Penalização):
            </label>
            <div class="input">
                <input type="number" step="0.01" min="0" value="<?php echo $logica['valor']; ?>" name="valor" id="input-valor" placeholder="Valor" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <div class="input">
                <input type="submit" value="Enviar" />
            </div>
        </div>

    </form>
</div>