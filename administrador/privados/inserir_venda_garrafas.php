<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1 && $tipo != 3  && $tipo != 4) {
    header('Location:/administrador/index.php');
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);
if ($_GET['id'] > 0) {
    $venda = $dbprivados->devolveVendaGarrafas($_GET['id']);
    foreach ($venda['garrafas'] as $garr_venda) {
        $garrafas_escolhidas[$garr_venda['id_garrafa']] = $garr_venda['quantidade'];
    }
    unset($venda['pagamento']);
    if ($venda['valor_multibanco'] > 0) {
        $venda['pagamento'][] = 1;
    }

    if ($venda['valor_dinheiro'] > 0) {
        $venda['pagamento'][] = 2;
    }
}
else if($_GET['id_reserva'] > 0) {
    $reserva = $dbprivados->devolveListaEsperaGarrafas($_GET['data_evento'], $_GET['id_reserva']);
    $reserva = $reserva[0];
    foreach ($reserva['garrafas'] as $garr_venda) {
        $garrafas_escolhidas[$garr_venda['id_garrafa']] = $garr_venda['quantidade'];
    }

    $venda['nome_cliente'] = $reserva['nome'];
    $venda['id_rp'] = $reserva['id_rp'];

    $venda['pagamento'][] = 1;
    $venda['valor_multibanco'] = $reserva['valor'];
    $venda['total'] = $reserva['valor'];
}
$bares = $dbprivados->listaBares();

if ($_POST) {
    $campos['hash'] = $_POST['hash'];
    $campos['nome_cliente'] = $_POST['nome_cliente'];
    $campos['id_rp'] = $_POST['id_rp'];
    $campos['id_bar'] = $_POST['id_bar'];
    $campos['numero_cartoes'] = $_POST['numero_cartoes'];
    $campos['pagamento'] = $_POST['pagamento'];
    $campos['valor_mbway'] = $_POST['valor_mbway'];
    $campos['valor_dinheiro'] = $_POST['valor_dinheiro'];
    $campos['valor_multibanco'] = $_POST['valor_multibanco'];
    $campos['total'] = $campos['valor_dinheiro'] + $campos['valor_multibanco']+ $campos['valor_mbway'];

    $campos['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $campos['ip'] = $_SERVER['REMOTE_ADDR'];
    if (date('H') < 14) {
        $data = date('Y-m-d', strtotime('-1 day'));
    } else {
        $data = date('Y-m-d');
    }

    #se for edição tem outro comportamento
    if ($venda['data_evento'] && intval($_GET['id']) > 0) {
        $campos['data_evento'] = $venda['data_evento'];
        $campos['id_processado'] = $venda['id_processado'];
        $campos['data'] = $venda['data'];
    } else {
        $campos['data_evento'] = $data;
        $campos['id_processado'] = $_SESSION['id_processado'];
        $campos['data'] = date('Y-m-d H:i:s');
    }


    if ($dbprivados->verificaGarrafasHash($campos['hash']) > 0 && empty($_GET['id'])) {
        $_SESSION['erro'] = "Esta compra já foi inserida anteriormente. Verifique na listagem de vendas se está registada. Obrigado";
        header('Location:/administrador/index.php?pg=venda_garrafas');
        exit;
    }
    if (empty($campos['nome_cliente']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo nome do cliente.";
    }
    if (empty($campos['id_rp']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Selecione um elemento do Staff.";
    }

    if (empty($campos['id_bar']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Selecione o bar da compra.";
    }
    if (empty($_POST['garrafas']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Por favor selecione as garrafas para compra.";
    }
    if (empty($campos['pagamento']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Selecione um método pagamento.";
    }
    if (empty($campos['valor_mbway']) && in_array(3, $campos['pagamento']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o valor de pagamento em MBWAY.";
    }
    if (empty($campos['valor_dinheiro']) && in_array(2, $campos['pagamento']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o valor de pagamento em dinheiro.";
    }
    if (empty($campos['valor_multibanco']) && in_array(1, $campos['pagamento']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o valor de pagamento em multibanco.";
    }

    if (empty($_SESSION['erro'])) {
        if ($_GET['id'] == 0) {
            $id_compra = $db->Insert('venda_garrafas_bar', $campos);
            $estado = $db->Insert('logs', array('descricao' => "Criou uma venda de garrafa bar.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção"));
            if ($id_compra) {
                $_SESSION['sucesso'] = "Compra efectuada com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao efectuar a compra!";
            }
        } else {
            $id_compra = $db->Update('venda_garrafas_bar', $campos, 'id=' . $_GET['id']);
            $estado = $db->Insert('logs', array('descricao' => "Alterou uma garrafa.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração"));
            if ($id_compra) {
                $_SESSION['sucesso'] = "Alterou a compra com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao alterar a compra!";
            }
        }
        if (empty($_SESSION['erro'])) {
            foreach ($_POST['garrafas'] as $id_garrafa => $quantidade) {
                $garrafa_campos['id_compra'] = $id_compra;
                $garrafa_campos['id_garrafa'] = $id_garrafa;
                $garrafa_campos['quantidade'] = $quantidade;
                $db->Insert('venda_garrafas_bar_garrafas', $garrafa_campos);
            }
        }
        // if ($_GET['data_evento']) {
        //     header('Location:/administrador/index.php?pg=garrafas_evento_data&data='.$_GET['data_evento']);
        // } else {

            header('Location:/administrador/index.php?pg=venda_garrafas');
        // }
        exit;
    }
}

if ($_GET['id'] == 0) {
    ?>
    <h1 class="titulo"> Compra - Garrafa </h1>
<?php

} else {
    ?>
    <h1 class="titulo"> Editar Compra - Garrafa </h1>
<?php
}
if ($campos) {
    $venda = $campos;
} else {
    $venda['hash'] = md5(time() . $_SESSION['id_utilizador'] . $_SESSION['id_processado']);
}
?>

<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" name="venda-garrafas" id="venda-garrafas" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" value="<?php echo $venda['hash']; ?>" name="hash" />
        <div class="input-grupo">
            <label for="input-nome">
                Nome do Ciente
            </label>
            <div class="input">
                <input type="text" value="<?php echo $venda['nome_cliente']; ?>" name="nome_cliente" id="input-nome-garrafa" class="teclado_virtual" placeholder="Nome do cliente" autocomplete="new-password" data-type="ajax" data-src="/administrador/privados/teclado.html.php" />
            </div>
        </div>
        <?php
        if ($tipo != 1) {
			// if(empty($venda['id_rp'])){
				$venda['id_rp'] = 103;
			// }
        ?>
            <input type="hidden" value="<?php echo $venda['id_rp']; ?>" name="id_rp" />
        <?php
        }
        else {
        ?>
            <div class="input-grupo">
                <label for="input-nome">
                    Staff
                </label>
                <div class="input staff">
                    <input type="hidden" value="<?php echo $venda['id_rp']; ?>" name="id_rp" />

                    <div class="staff-escolhido">
                        <?php
                        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/ajax/staff.html.php";
                        ?>
                    </div>
                    <a data-type="ajax" data-src="/administrador/privados/ajax/rps.ajax.php" href="javascript:;" class="adicionar"><?php if (intval($_GET['id']) > 0) { ?> Alterar <?php } else { ?> Adicionar<?php } ?> Staff</a>
                </div>
            </div>
        <?php
    }
    ?>
        <div class="input-grupo">
            <label for="input-nome">
                Bar
            </label>
            <div class="input">
                <select name="id_bar" id="input-bar">
                    <option value="">Escolha um bar</option>
                    <?php
                    foreach ($bares as $bar) {
                        ?>
                        <option value="<?php echo $bar['id']; ?>" <?php if ($venda['id_bar'] == $bar['id']) { ?> selected="selected" <?php } ?>> <?php echo $bar['nome']; ?> </option>
                    <?php
                }
                ?>
                </select>
            </div>
        </div>

        <div class="input-grupo">
            <label for="input-nome">
                Garrafas
            </label>
            <div class="garrafas">
                <div class="escolha-garrafas-responsive">
                    <?php
                    include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/ajax/inputs_garrafas.html.php";
                    ?>
                </div>
                <a data-fancybox data-type="ajax" data-src="/administrador/privados/ajax/garrafas.ajax.php" href="javascript:;" class="adicionar">Adicionar garrafas </a>
            </div>
        </div>

        <div class="input-grupo">
            <label for="input-nome">
                Numero de cartões
            </label>
            <div class="input">
                <input type="number" data-decimal="0" min="0" value="<?php echo $venda['numero_cartoes']; ?>" name="numero_cartoes" class="teclado_numerico" id="input-nome-cartoes" placeholder="Número de cartões" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Pagamento
            </label>
            <div class="input">
                <div class="pagamentos">
                    <div class="pagamento">
                        <input type="checkbox" value="1" <?php if (in_array(1, $venda['pagamento'])) { ?> checked="checked" <?php } ?> name="pagamento[]" id="pagamento-1" data-show="input-valor-multibanco" />
                        <label for="pagamento-1">
                            <span class="icon">
                                <img src="/temas/administrador/imagens/multibanco.svg">
                            </span>
                            <span class="texto">
                                Multibanco
                            </span>
                        </label>
                    </div>
                    <div class="pagamento">
                        <input type="checkbox" value="2" <?php if (in_array(2, $venda['pagamento'])) { ?> checked="checked" <?php } ?> name="pagamento[]" id="pagamento-2" data-show="input-valor-dinheiro" />
                        <label for="pagamento-2">
                            <span class="icon">
                                <img src="/temas/administrador/imagens/dinheiro.svg">
                            </span>
                            <span class="texto">
                                Dinheiro
                            </span>
                        </label>
                    </div>
                    <div class="pagamento">
                        <input type="checkbox" value="3" <?php if (in_array(3, $venda['pagamento'])) { ?> checked="checked" <?php } ?> name="pagamento[]" id="pagamento-3" data-show="input-valor-mbway" />
                        <label for="pagamento-3">
                            <span class="icon">
                                <img src="/temas/administrador/imagens/mbway.png">
                            </span>
                            <span class="texto">
                                MBWAY
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="input-grupo <?php if (!in_array(1, $venda['pagamento'])) { ?> hidden <?php } ?>" id="input-valor-multibanco">
            <label for="input-valor-multibanco ">
                Valor em Multibanco (€)
            </label>
            <div class=" input ">
                <input type="number" step="0.01" min="0" value="<?php echo $venda['valor_multibanco']; ?>" class="teclado_numerico" data-decimal="1" name="valor_multibanco" id="input-valor-multibanco" placeholder="Valor pago em multibanco" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo <?php if (!in_array(2, $venda['pagamento'])) { ?> hidden <?php } ?>" id="input-valor-dinheiro">
            <label for="input-valor-dinheiro">
                Valor em Dinheiro (€)
            </label>
            <div class="input">
                <input type="number" step="0.01" min=" 0" value="<?php echo $venda['valor_dinheiro']; ?>" class="teclado_numerico" data-decimal="1" name="valor_dinheiro" id="input-valor-dinheiro" placeholder="Valor pago em dinheiro" autocomplete="new-password" />
            </div>
        </div>

        <div class="input-grupo <?php if (!in_array(3, $venda['pagamento'])) { ?> hidden <?php } ?>" id="input-valor-mbway">
            <label for="input-valor-mbway">
                Valor em MBWAY (€)
            </label>
            <div class="input">
                <input type="number" step="0.01" min=" 0" value="<?php echo $venda['valor_mbway']; ?>" class="teclado_numerico" data-decimal="1" name="valor_mbway" id="input-valor-mbway" placeholder="Valor pago em mbway" autocomplete="new-password" />
            </div>
        </div>

        <div class="input-grupo">
            <label for="input-nome">
                Valor total (€)
            </label>
            <div class="input valor-total">
                <span class="valor"><?php echo (double)$venda['total']; ?></span><span class="simbolo"> € </span>
                <small>(Valor calculado na soma do pagamento em dinheiro e multibanco.)</small>
            </div>
        </div>
        <div class="input-grupo">
            <div class="input">
                <input type="submit" value="Finalizar" />
            </div>
        </div>

    </form>
</div>