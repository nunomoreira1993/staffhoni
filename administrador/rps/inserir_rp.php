<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1) {
    header('Location:/administrador/index.php');
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
if ($_GET['id'] > 0) {
    $rp = $dbrps->devolveRP($_GET['id']);
}

$rps_produtores = $dbrps->listaRps(false, false, false, array($dbrps->getIdProdutor()));
$rps_chefes = $dbrps->listaRps(false, false, false, array($dbrps->getIdChefeEquipa()));


$cargos = $dbrps->listaCargos();
if ($_POST) {
    $campos['email'] = $_POST['email'];
    $campos['password'] = base64_encode($_POST['password']);
    $campos['nome'] = $_POST['nome'];
    $campos['pin'] = $_POST['pin'];
    $campos['id_chefe_equipa'] = $_POST['id_chefe_equipa'];
    $campos['id_produtor'] = $_POST['id_produtor'];
    $campos['data_minima_pagamentos'] = $_POST['data_minima_pagamentos'];
    $campos['salario'] = $_POST['salario'];
    $campos['telemovel'] = intval($_POST['telemovel']);
    $campos['id_cargo'] = intval($_POST['id_cargo']);
    $campos['permite_apagar_privados'] = intval($_POST['permite_apagar_privados']);
    $campos['penaliza_convite'] = intval($_POST['penaliza_convite']);
    $campos['comissao_guest'] = intval($_POST['comissao_guest']);
    $campos['disponibilidade_mesas'] = intval($_POST['disponibilidade_mesas']);
    $campos['permite_reservar_privados'] = intval($_POST['permite_reservar_privados']);


    if (empty($campos['email']) && empty($campos['telemovel']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo e-mail ou telemovel.";
    }
    if (empty($campos['password']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha corretamente o campo password";
    }
    if (empty($campos['nome']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha corretamente o campo nome";
    }
    if (empty($campos['id_cargo']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Por favor escolha um cargo.";
    }
    if ($_FILES['foto']['name'] && empty($_SESSION['erro'])) {
        $foto_importada = doUpload($_FILES['foto'], "/rps/originais/", "foto");
        $resize = doResize("/fotos/rps/originais/", $foto_importada, "/fotos/rps/", $foto_importada, 4, "center", 400, 400);

        if ($resize['success'] == true) {
            $campos['foto'] = $foto_importada;
        } else {
            $_SESSION['erro'] = $resize['errors']['user'][0];
        }

        if ($_GET['id'] && empty($_SESSION['erro'])) {
            if ($rp['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/rps/" . $rp['foto'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . "/fotos/rps/" . $rp['foto']);
            }
        }
    }
    if (empty($_SESSION['erro'])) {
        if ($_GET['id'] == 0) {
            $db->Insert('rps', $campos);
            $estado = $db->Insert('logs', array('descricao' => "Inseriu um RP", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
            if ($estado) {
                $_SESSION['sucesso'] = "Inseriu o RP com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao inserir o RP!";
            }
        } else {
            $db->Update('rps', $campos, 'id=' . $_GET['id']);
            $estado = $db->Insert('logs', array('descricao' => "Alterou um RP", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
            if ($estado) {
                $_SESSION['sucesso'] = "Alterou o RP com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao alterar o RP!";
            }
        }
        header('Location:/administrador/index.php?pg=rps');
        exit;
    }
}

if ($_GET['id'] == 0) {
?>
    <h1 class="titulo"> Inserir - RP </h1>
<?php

} else {
?>
    <h1 class="titulo"> Editar - RP </h1>
<?php
}
if ($campos) {
    $administrador = $campos;
}
?>

<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" name="inserir_rp" id="inserir_rp" enctype="multipart/form-data" autocomplete="off">
        <?php
        /*
        ?>
        <div class="input-grupo">
            <label for="input-email">
                E-mail
            </label>
            <div class="input">
                <input type="email" value="<?php echo $rp['email']; ?>" name="email" id="input-email-rp" placeholder="E-mail para efectuar login" autocomplete="new-password" />
            </div>
        </div>
        <?php
        */
        ?>
        <div class="input-grupo">
            <label for="input-email">
                Nº Telemóvel
            </label>
            <div class="input">
                <input type="number" value="<?php echo $rp['telemovel']; ?>" name="telemovel" id="input-email-rp" placeholder="Telemovel para efectuar login" autocomplete="off" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-password">
                Password
            </label>
            <div class="input">
                <input type="password" value="<?php echo base64_decode($rp['password']); ?>" name="password" id="input-password" placeholder="Password para efectuar login" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Nome
            </label>
            <div class="input">
                <input type="text" value="<?php echo $rp['nome']; ?>" name="nome" id="input-nome" placeholder="Nome" />
            </div>
        </div>
        <div class="input-grupo ">
            <label for="input-id_cargo">
                Cargo
            </label>
            <div class="input">
                <select name="id_cargo">
                    <option value="0"> Selecione um cargo </option>
                    <?php
                    foreach ($cargos as $cargo) {
                    ?>
                        <option value="<?php echo $cargo['id']; ?>" <?php echo ($cargo['chefe_equipa'] == 1) ? "data-chefe-equipa='1'" : ""; ?> <?php echo ($cargo['produtor'] == 1) ? "data-produtor='1'" : ""; ?> <?php if ($rp['id_cargo'] == $cargo['id']) { ?> selected="selected" <?php } ?>> <?php echo $cargo['nome']; ?> </option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="input-grupo hidden">
            <label for="input-id_produtor">
                Produtor
            </label>
            <div class="input">
                <select name="id_produtor">
                    <option value="0"> Selecione um produtor </option>
                    <?php
                    foreach ($rps_produtores as $rp_produtor) {
                    ?>
                        <option value="<?php echo $rp_produtor['id']; ?>" <?php if ($rp['id_produtor'] == $rp_produtor['id']) { ?> selected="selected" <?php } ?>> <?php echo $rp_produtor['nome']; ?> </option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="input-grupo hidden">
            <label for="input-id_chefe_equipa">
                Chefe de Equipa
            </label>
            <div class="input">
                <select name="id_chefe_equipa">
                    <option value="0"> Selecione um chefe de equipa </option>
                    <?php
                    foreach ($rps_chefes as $rp_chefe) {
                    ?>
                        <option value="<?php echo $rp_chefe['id']; ?>" <?php if ($rp['id_chefe_equipa'] == $rp_chefe['id']) { ?> selected="selected" <?php } ?>> <?php echo $rp_chefe['nome']; ?> </option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-foto">
                Foto
            </label>
            <div class="input">
                <?php
                if ($rp['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/rps/" . $rp['foto'])) {
                ?>
                    <div class="foto">
                        <img src="/fotos/rps/<?php echo $rp['foto']; ?>" width="150px">
                    </div>
                <?php
                }
                ?>
                <input type="file" value="<?php echo $rp['foto']; ?>" name="foto" id="input-foto" placeholder="Foto de perfil" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Valor da sessão
            </label>
            <div class="input">
                <input type="text" value="<?php echo $rp['salario']; ?>" name="salario" id="input-salario" placeholder="Valor da Sessão" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Data de mínima para pagamento
            </label>
            <div class="input">
                <input type="date" value="<?php echo $rp['data_minima_pagamentos'] ? $rp['data_minima_pagamentos'] : date('Y-m-d') ; ?>" name="data_minima_pagamentos" id="input-data_minima_pagamentos" placeholder="Data mínima para pagamentos" />
            </div>
        </div>

        <div class="input-grupo">
            <label for="input-nome">
                Permite apagar privados
            </label>
            <div class="input">
                <select name="permite_apagar_privados" id="input-permite_apagar_privados">
                    <option <?php if ($rp['permite_apagar_privados'] == 0) { ?> selected="selected" <?php } ?> value="0"> Não </option>

                    <option <?php if ($rp['permite_apagar_privados'] == 1) { ?> selected="selected" <?php } ?> value="1"> Sim </option>
                </select>
            </div>
        </div>

        <div class="input-grupo">
            <label for="input-nome">
                Recebe comissão Guest
            </label>
            <div class="input">
                <select name="comissao_guest" id="input-comissao_guest">
                    <option <?php if ($rp['comissao_guest'] == 0) { ?> selected="selected" <?php } ?> value="0"> Não </option>

                    <option <?php if ($rp['comissao_guest'] == 1) { ?> selected="selected" <?php } ?> value="1"> Sim </option>
                </select>
            </div>
        </div>
        <?php
        /*
        ?>
        <div class="input-grupo">
            <label for="input-nome">
                Penaliza envio Convite Guest
            </label>
            <div class="input">
                <select name="penaliza_convite" id="input-penaliza_convite">
                    <option <?php if ($rp['penaliza_convite'] == 0) { ?> selected="selected" <?php } ?> value="0"> Não </option>
                    <option <?php if ($rp['penaliza_convite'] == 1) { ?> selected="selected" <?php } ?> value="1"> Sim </option>
                </select>
            </div>
        </div>
        <?php
        */
        ?>
        <div class="input-grupo">
            <label for="input-nome">
                Permite ver Disponibilidade de mesas
            </label>
            <div class="input">
                <select name="disponibilidade_mesas" id="input-disponibilidade_mesas">
                    <option <?php if ($rp['disponibilidade_mesas'] == 0) { ?> selected="selected" <?php } ?> value="0"> Não </option>

                    <option <?php if ($rp['disponibilidade_mesas'] == 1) { ?> selected="selected" <?php } ?> value="1"> Sim </option>
                </select>
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Permite Reservar Privados
            </label>
            <div class="input">
                <select name="permite_reservar_privados" id="input-disponibilidade_mesas">
                    <option <?php if ($rp['permite_reservar_privados'] == 0) { ?> selected="selected" <?php } ?> value="0"> Não </option>
                    <option <?php if ($rp['permite_reservar_privados'] == 1) { ?> selected="selected" <?php } ?> value="1"> Sim </option>
                </select>
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                PIN
            </label>
            <div class="input">
                <input type="text" value="<?php echo $rp['pin']; ?>" name="pin" id="input-pin" placeholder="Pin" />
            </div>
        </div>

        <div class="input-grupo">
            <div class="input">
                <input type="submit" value="Enviar" />
            </div>
        </div>

    </form>
</div>