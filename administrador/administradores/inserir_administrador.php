<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1) {
    header('Location:/administrador/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/administradores/administrador.obj.php');
$dbadministrador = new administrador($db);
if ($_GET['id'] > 0) {
    $administrador = $dbadministrador->devolveUtilizador($_GET['id']);
}
if ($_POST) {
    $campos['email'] = $_POST['email'];
    $campos['password'] = base64_encode($_POST['password']);
    $campos['nome'] = $_POST['nome'];
    $campos['tipo'] = $_POST['tipo'];
    $campos['telemovel'] = $_POST['telemovel'];
    $campos['pagamento_caixa'] = $_POST['pagamento_caixa'];

    if (empty($campos['email']) && empty($campos['telemovel']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo e-mail ou telemovel.";
    }
    if (empty($campos['password']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha corretamente o campo password";
    }
    if (empty($campos['nome']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha corretamente o campo nome";
    }
    if (empty($campos['tipo']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha corretamente o campo tipo";
    }
    if ($_FILES['foto']['name'] && empty($_SESSION['erro'])) {
        $foto_importada = doUpload($_FILES['foto'], "/administradores/originais/", "foto");
        $resize  = doResize("/fotos/administradores/originais/", $foto_importada, "/fotos/administradores/", $foto_importada, 4, "center", 400, 400);
        if ($resize['success'] == true) {
            $campos['foto'] = $foto_importada;
        } else {
            $_SESSION['erro'] = $resize['errors']['user'][0];
        }

        if ($_GET['id'] && empty($_SESSION['erro'])) {
            if ($administrador['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/administradores/" . $administrador['foto'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . "/fotos/administradores/" . $administrador['foto']);
            }
        }
    }
    if (empty($_SESSION['erro'])) {
        if ($_GET['id'] == 0) {
            $db->Insert('administradores', $campos);
            $_SESSION['sucesso'] = "Inseriu o administrador com sucesso";
            $db->Insert('logs', array('descricao' => "Inseriu um administrador", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        } else {
            $db->Update('administradores', $campos, 'id=' . $_GET['id']);
            $_SESSION['sucesso'] = "Alterou o administrador com sucesso";
            $db->Insert('logs', array('descricao' => "Alterou um administrador", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        }
        header('Location:/administrador/index.php?pg=administradores');
        exit;
    }
}

if ($_GET['id'] == 0) {
    ?>
    <h1 class="titulo"> Inserir - Administrador </h1>
<?php

} else {
    ?>
    <h1 class="titulo"> Editar - Administrador </h1>
<?php
}
if ($campos) {
    $administrador = $campos;
}
?>

<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="input-grupo">
            <label for="input-email">
                E-mail
            </label>
            <div class="input">
                <input type="email" value="<?php echo $administrador['email']; ?>" name="email" id="input-email" placeholder="E-mail para efectuar login" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-email">
                Nº Telemóvel
            </label>
            <div class="input">
                <input type="number" value="<?php echo $administrador['telemovel']; ?>" name="telemovel" id="input-email" placeholder="Telemovel para efectuar login" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-password">
                Password
            </label>
            <div class="input">
                <input type="password" value="<?php echo base64_decode($administrador['password']); ?>" name="password" id="input-password" placeholder="Password para efectuar login" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Nome
            </label>
            <div class="input">
                <input type="text" value="<?php echo $administrador['nome']; ?>" name="nome" id="input-nome" placeholder="Nome" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-foto">
                Foto
            </label>
            <div class="input">
                <?php
                if (!empty($administrador['foto']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/administradores/" . $administrador['foto'])) {
                    ?>
                    <div class="foto">
                        <img src="/fotos/administradores/<?php echo $administrador['foto']; ?>" width="150px">
                    </div>
                <?php

            }
            ?>
                <input type="file" value="<?php echo $administrador['foto']; ?>" name="foto" id="input-foto" placeholder="Foto de perfil" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-email">
                Tipo de utilizador
            </label>
            <div class="input">
                <select name="tipo">
                    <option value="1" <?php if ($administrador['tipo'] == 1) { ?> selected="selected" <?php } ?>> Administrador </option>
                    <option value="2" <?php if ($administrador['tipo'] == 2) { ?> selected="selected" <?php } ?>> Staff </option>
                    <option value="6" <?php if ($administrador['tipo'] == 6) { ?> selected="selected" <?php } ?>> Recepção </option>
                    <option value="7" <?php if ($administrador['tipo'] == 7) { ?> selected="selected" <?php } ?>> Entrada Privados </option>
                    <option value="3" <?php if ($administrador['tipo'] == 3) { ?> selected="selected" <?php } ?>> Privados </option>
                    <option value="4" <?php if ($administrador['tipo'] == 4) { ?> selected="selected" <?php } ?>> Caixa </option>
                    <option value="5" <?php if ($administrador['tipo'] == 5) { ?> selected="selected" <?php } ?>> Pagamentos </option>
                </select>
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-email">
                Pagamentos desconta ao valor de caixa
            </label>
            <div class="input">
                <select name="pagamento_caixa">
                    <option value="0" <?php if ($administrador['pagamento_caixa'] == 0) { ?> selected="selected" <?php } ?>> Não </option>
                    <option value="1" <?php if ($administrador['pagamento_caixa'] == 1) { ?> selected="selected" <?php } ?>> Sim </option>
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