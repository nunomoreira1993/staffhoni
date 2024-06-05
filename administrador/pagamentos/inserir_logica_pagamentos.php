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
    $logica = $dbpagamentos->devolveLogicaPagamentos($_GET['id']);
}
if ($_POST) {
    $campos['de'] = $_POST['de'];
    $campos['ate'] = $_POST['ate'];
    $campos['valor'] = $_POST['valor'];
    if (strlen($campos['de']) == 0 && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo De.";
    }
    if (strlen($campos['ate']) == 0 && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo Até.";
    }
    if (empty($_SESSION['erro'])) {
        if ($_GET['id'] == 0) {
            
            $db->Insert('logica_pagamentos', $campos);
            $estado = $db->Insert('logs', array('descricao' => "Inseriu um registo de lógica de pagamento.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção"));
            if ($estado) {
                $_SESSION['sucesso'] = "Inseriu um registo de lógica de pagamento com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao inserir um registo de lógica de pagamento!";
            }
        } else {
            $db->Update('logica_pagamentos', $campos, 'id=' . $_GET['id']);
            $estado = $db->Insert('logs', array('descricao' => "Alterou um registo de lógica de pagamento.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração"));
            if ($estado) {
                $_SESSION['sucesso'] = "Alterou um registo de lógica de pagamento com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao alterar registo de lógica de pagamento!";
            }
        }
        header('Location:/administrador/index.php?pg=logica_pagamentos');
        exit;
    }
}

if ($_GET['id'] == 0) {
    ?>
    <h1 class="titulo"> Inserir - Linha de lógica de pagamentos </h1>
<?php
} else {
    ?>
    <h1 class="titulo"> Editar - Linha de lógica de pagamentos </h1>
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
                De:
            </label>
            <div class="input">
                <input type="number" value="<?php echo $logica['de']; ?>" name="de" id="input-de" placeholder="De" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Até:
            </label>
            <div class="input">
                <input type="number" value="<?php echo $logica['ate']; ?>" name="ate" id="input-ate" placeholder="Até" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Valor (€):
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