<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1 && $tipo != 5) {
    header('Location:/administrador/index.php');
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);

if ($_GET['id'] > 0) {
    $convite = $dbpagamentos->devolveConvite($_GET['id']);
}

if ($_POST) {
    $campos['data_evento'] = $_POST['data_evento'];
    if (empty($campos['data_evento']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo de Data de Evento.";
    }
    if (empty($_SESSION['erro'])) {
        if ($_GET['id'] != 0) {
            $db->Update('convites', $campos, 'id=' . $_GET['id']);
            $estado = $db->Insert('logs', array('descricao' => "Alterou um convite.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração"));
            if ($estado) {
                $_SESSION['sucesso'] = "Alterou um convite com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao alterar convite!";
            }
        }
        header('Location:/administrador/index.php?pg=ver_convites&data=' . $convite['data_evento']);
        exit;
    }
}

if ($_GET['id'] == 0) {
    ?>
    <h1 class="titulo"> Inserir - Convite</h1>
<?php
} else {
    ?>
    <h1 class="titulo"> Editar - Convite </h1>
<?php
}
if ($campos) {
    $convite = $campos;
}
?>

<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" name="inserir_presencas" id="inserir_presencas" enctype="multipart/form-data" autocomplete="off">
        <div class="input-grupo">
            <label for="input-nome">
                Nome:
            </label>
            <div class="input">
                <?php echo $convite['nome']; ?>
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Data de Submissao:
            </label>
            <div class="input">
                <?php echo $convite['data']; ?>
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Data de Evento:
            </label>
            <div class="input">
                <input type="date" value="<?php echo $convite['data_evento']; ?>" name="data_evento" id="input-data_evento" placeholder="Data de Evento" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <div class="input">
                <input type="submit" value="Enviar" />
            </div>
        </div>

    </form>
</div>