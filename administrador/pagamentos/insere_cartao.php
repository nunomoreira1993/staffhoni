<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1 && $tipo != 4 && $tipo != 2) {
    header('Location:/administrador/index.php');
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);

if (date('H') < 14) {
    $data_evento = date('Y-m-d', strtotime('-1 day'));
} else {
    $data_evento = date('Y-m-d');
}

if ($_GET['id'] > 0) {
    $presenca = $dbpagamentos->devolvePresencas($_GET['id']);
}
if ($_POST) {
    $campos['data_evento'] = $data_evento;
    $campos['data_entrada'] = date('Y-m-d H:i:s');
    $campos['nome'] = $_POST['nome'];
    $campos['numero_cartao'] = $_POST['numero_cartao'];
    $campos['ip'] = $_SERVER['REMOTE_ADDR'];
    $campos['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    if (empty($campos['nome']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo Nome.";
    }
    if (empty($campos['numero_cartao']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo Número de cartão.";
    }
    if (empty($_SESSION['erro'])) {
        if ($_GET['id'] == 0) {
            $db->Insert('presencas', $campos);
            $estado = $db->Insert('logs', array('descricao' => "Inseriu um cartão extra.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção"));
            if ($estado) {
                $_SESSION['sucesso'] = "Inseriu um cartão extra com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao inserir um  cartão extra!";
            }
        } else {
            $db->Update('presencas', $campos, 'id=' . $_GET['id']);
            $estado = $db->Insert('logs', array('descricao' => "Alterou um cartão extra.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração"));
            if ($estado) {
                $_SESSION['sucesso'] = "Alterou um registo cartão extra com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao alterar cartão extra de atrasos!";
            }
        }
        header('Location:/administrador/index.php?pg=ver_entradas&data='.$data_evento);
        exit;
    }
}

if ($_GET['id'] == 0) {
    ?>
    <h1 class="titulo"> Inserir - Cartão Extra</h1>
<?php
} else {
    ?>
    <h1 class="titulo"> Editar - Cartão Extra </h1>
<?php
}
if ($campos) {
    $presenca = $campos;
}
?>

<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" name="inserir_presencas" id="inserir_presencas" enctype="multipart/form-data" autocomplete="off">
        <div class="input-grupo">
            <label for="input-nome">
                Nome:
            </label>
            <div class="input">
                <input type="text" value="<?php echo $presenca['nome']; ?>" name="nome" id="input-nome" placeholder="Nome (Extra)" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Número de cartão:
            </label>
            <div class="input">
                <input type="number" value="<?php echo $presenca['numero_cartao']; ?>" name="numero_cartao" id="input-numero_cartao" placeholder="Número de Cartão" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <div class="input">
                <input type="submit" value="Enviar" />
            </div>
        </div>

    </form>
</div>