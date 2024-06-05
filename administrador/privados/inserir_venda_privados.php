<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1 && $tipo != 3 && $tipo != 4) {
    header('Location:/administrador/index.php');
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);
if ($_GET['id'] > 0) {
    $venda = $dbprivados->devolveVendaPrivados($_GET['id']);
}

$mesa = $dbprivados->devolveMesa($_GET['id_mesa']);
$sala = $dbprivados->devolveSala($mesa['id_sala']);

if (empty($mesa) || empty($sala)) {
    header('Location:/administrador/index.php');
    exit;
}
if (date('H') < 14) {
    $data = date('Y-m-d', strtotime('-1 day'));
} else {
    $data = date('Y-m-d');
}

$reservaDataEvento = $dbprivados->devolveReservaDataEvento($_GET['id_mesa'], $data);

if ($dbprivados->verificaMesaVendida($_GET['id_mesa'], $data)) {
    $reservaDataEvento['valor_dinheiro_adiantado'] = 0;
    $reservaDataEvento['valor_multibanco_adiantado'] = 0;
    $reservaDataEvento['valor_mbway_adiantado'] = 0;
}


if ($_POST) {
    $campos['hash'] = $_POST['hash'];
    $campos['id_reserva'] = $_POST['id_reserva'];
    $campos['id_mesa'] = $_POST['id_mesa'];
    $campos['nome_cliente'] = $_POST['nome_cliente'];
    $campos['id_processado'] = $_SESSION['id_processado'];
    $campos['id_rp'] = $_POST['id_rp'];
    $campos['id_gerente'] = $_POST['id_gerente'];
    $campos['numero_cartoes'] = $_POST['numero_cartoes'];
    $campos['pagamento'] = $_POST['pagamento'];
    $campos['valor_dinheiro'] = (double) $_POST['valor_dinheiro'];
    $campos['valor_multibanco'] = (double) $_POST['valor_multibanco'];
    $campos['valor_mbway'] = (double) $_POST['valor_mbway'];
    $campos['valor_dinheiro_adiantado'] = $reservaDataEvento['valor_dinheiro_adiantado'];
    $campos['valor_multibanco_adiantado'] = $reservaDataEvento['valor_multibanco_adiantado'];
    $campos['valor_mbway_adiantado'] = $reservaDataEvento['valor_mbway_adiantado'];
    $campos['total_venda'] = $campos['valor_dinheiro'] + $campos['valor_multibanco'] + $campos['valor_mbway'];
    $campos['total'] = $campos['total_venda'] + $campos['valor_dinheiro_adiantado'] + $campos['valor_multibanco_adiantado'] + $campos['valor_mbway_adiantado'];
    $campos['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $campos['ip'] = $_SERVER['REMOTE_ADDR'];

    $campos['data_evento'] = $data;
    $campos['data'] = date('Y-m-d H:i:s');

    if ($dbprivados->verificaPrivadoHash($campos['hash']) > 0 && empty($_GET['id'])) {
        $_SESSION['erro'] = "A reserva já foi inserida anteriormente.";
        header('Location:/administrador/index.php?pg=venda_privados');
        exit;
    }
    if (empty($campos['id_mesa']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Por favor escolha uma mesa.";
    }

    if (empty($campos['nome_cliente']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo nome do cliente.";
    }
    if (empty($campos['id_rp']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Selecione um elemento do Staff.";
    }
    if (empty($campos['id_gerente']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Selecione um gerente.";
    }
    if (empty($_POST['garrafas']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Por favor selecione as garrafas para compra.";
    }
    if (empty($campos['numero_cartoes']) && empty($_SESSION['erro'])) {
        $_SESSION['erro'] = "Preêncha o campo número de cartões.";
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
            $id_compra = $db->Insert('venda_privados', $campos);
            $estado = $db->Insert('logs', array('descricao' => "Criou uma venda de privados para a mesa ID " . $campos['id_mesa'], 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Inserção", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
            if ($id_compra) {
                $_SESSION['sucesso'] = "Compra efectuada com sucesso";
            } else {
                $_SESSION['erro'] = "Erro ao efectuar a compra!";
            }
        } else {
            $id_compra = $db->Update('venda_privados', $campos, 'id=' . $_GET['id']);
            $estado = $db->Insert('logs', array('descricao' => "Alterou uma venda de privados.", 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Alteração", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
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
                $db->Insert('venda_privados_garrafas', $garrafa_campos);
            }
        }
        header('Location:/administrador/index.php?pg=venda_privados');
        exit;
    }
}

if ($_GET['id'] == 0) {
?>
    <h1 class="titulo"> Compra - Privados </h1>
<?php

} else {
?>
    <h1 class="titulo"> Editar Compra - Privados </h1>
<?php
}
if ($campos) {
    $venda = $campos;
    $hidden = 0;
} else {
    $venda = $reservaDataEvento;
    $venda['id_reserva'] = $reservaDataEvento['id'];
    $venda['hash'] = md5(time() . $_SESSION['id_utilizador'] . $_SESSION['id_processado']);
}
?>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" name="venda-privados" id="venda-privados" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" value="<?php echo $venda['hash']; ?>" name="hash" />
        <input type="hidden" value="<?php echo $venda['id_reserva']; ?>" name="id_reserva" />
        <input type="hidden" value="<?php echo $_GET['id_mesa']; ?>" name="id_mesa" />
        <div class="input-grupo">
            <label for="input-nome">
                Mesa
            </label>
            <div class="info">
                <b> Àrea: </b> <?php echo $sala['nome']; ?>
                <br />
                <b> Mesa: </b> <?php echo $mesa['codigo_mesa']; ?>
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Nome do Ciente
            </label>
            <div class="input">
                <input type="text" value="<?php echo $venda['nome_cliente']; ?>" name="nome_cliente" id="input-nome-garrafa" placeholder="Nome do cliente" autocomplete="new-password" <?php if ($hidden == 0) { ?> class="teclado_virtual" <?php } else { ?> readonly="readonly" <?php } ?> />
            </div>
        </div>
        <div class="input-grupo">
            <label for="input-nome">
                Angariador
            </label>
            <div class="input staff">
                <input type="hidden" value="<?php echo $venda['id_rp']; ?>" name="id_rp" />
                <div class="staff-escolhido">
                    <?php
                    include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/ajax/staff.html.php";
                    ?>
                </div>
                <?php
                if ($hidden == 0) {
                ?>
                    <a data-type="ajax" data-src="/administrador/privados/ajax/rps.ajax.php" href="javascript:;" class="adicionar">Adicionar Angariador</a>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="input-grupo">
            <label for="input-nome">
                Gerente
            </label>
            <div class="input staff">
                <input type="hidden" value="<?php echo $venda['id_gerente']; ?>" name="id_gerente" />
                <div class="staff-escolhido">
                    <?php
                    $venda['id_rp'] = $venda['id_gerente'];
                    include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/ajax/staff.html.php";
                    ?>
                </div>

                <?php
                if ($hidden == 0) {
                ?>
                    <a data-type="ajax" data-src="/administrador/privados/ajax/rps.ajax.php?gerente=1" href="javascript:;" class="adicionar">Adicionar Gerente</a>
                <?php
                }
                ?>
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
                <input type="number" min="0" value="<?php echo $venda['numero_cartoes']; ?>" name="numero_cartoes" class="teclado_numerico" id="input-nome-cartoes" placeholder="Número de cartões" data-decimal="0" autocomplete="new-password" />
            </div>
        </div>

        <?php
        if ($venda['valor_multibanco_adiantado'] > 0) {
        ?>
            <div class="input-grupo">
                <label for="input-valor-multibanco ">
                    Valor em Multibanco (ADIANTADO) (€)
                </label>
                <div class="input">
                    <?php echo euro($venda['valor_multibanco_adiantado']); ?>
                </div>
            </div>
        <?php
        }
        if ($venda['valor_dinheiro_adiantado'] > 0) {
        ?>
            <div class="input-grupo">
                <label for="input-valor-dinheiro">
                    Valor em Dinheiro (ADIANTADO) (€)
                </label>
                <div class="input">
                    <?php echo euro($venda['valor_dinheiro_adiantado']); ?>
                </div>
            </div>
        <?php
        }
        if ($venda['valor_mbway_adiantado'] > 0) {
        ?>
            <div class="input-grupo">
                <label for="input-valor-dinheiro">
                    Valor em MBWAY (ADIANTADO) (€)
                </label>
                <div class="input">
                    <?php echo euro($venda['valor_mbway_adiantado']); ?>
                </div>
            </div>
        <?php
        }
        ?>
        <div class="input-grupo">
            <label for="input-nome">
                Pagamento
            </label>
            <div class="input">
                <div class="pagamentos">
                    <div class="pagamento">
                        <input type="checkbox" value="1" <?php if ($venda['pagamento'] && in_array(1, $venda['pagamento'])) { ?> checked="checked" <?php } ?> name="pagamento[]" id="pagamento-1" data-show="input-valor-multibanco" />
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
                        <input type="checkbox" value="2" <?php if ($venda['pagamento'] && in_array(2, $venda['pagamento'])) { ?> checked="checked" <?php } ?> name="pagamento[]" id="pagamento-2" data-show="input-valor-dinheiro" />
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
                        <input type="checkbox" value="3" <?php if ($venda['pagamento'] && in_array(3, $venda['pagamento'])) { ?> checked="checked" <?php } ?> name="pagamento[]" id="pagamento-3" data-show="input-valor-mbway" />
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

        <div class="input-grupo <?php if (empty($venda['pagamento']) || (is_array($venda['pagamento']) && !in_array(1, $venda['pagamento']))) { ?> hidden <?php } ?>" id="input-valor-multibanco">
            <label for="input-valor-multibanco ">
                Valor em Multibanco (€)
            </label>
            <div class=" input ">
                <input type="number" step="0.01" min="0" value="<?php echo $venda['valor_multibanco']; ?>" class="teclado_numerico" data-decimal="1" name="valor_multibanco" id="input-valor-multibanco" placeholder="Valor pago em multibanco" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo <?php if (empty($venda['pagamento']) || (is_array($venda['pagamento']) && !in_array(2, $venda['pagamento']))) { ?> hidden <?php } ?>" id="input-valor-dinheiro">
            <label for="input-valor-dinheiro">
                Valor em Dinheiro (€)
            </label>
            <div class="input">
                <input type="number" step="0.01" min=" 0" value="<?php echo $venda['valor_dinheiro']; ?>" class="teclado_numerico" data-decimal="1" name="valor_dinheiro" id="input-valor-dinheiro" placeholder="Valor pago em dinheiro" autocomplete="new-password" />
            </div>
        </div>
        <div class="input-grupo <?php if (empty($venda['pagamento']) || (is_array($venda['pagamento']) && !in_array(3, $venda['pagamento']))) { ?> hidden <?php } ?>" id="input-valor-mbway">
            <label for="input-valor-mbway">
                Valor em MBWAY (€)
            </label>
            <div class="input">
                <input type="number" step="0.01" min=" 0" value="<?php echo $venda['valor_mbway']; ?>" class="teclado_numerico" data-decimal="1" name="valor_mbway" id="input-valor-mbway" placeholder="Valor pago em mbway" autocomplete="new-password" />
            </div>
        </div>

        <div class="input-grupo">
            <label for="input-nome">
                Valor total pagamento (€)
            </label>
            <div class="input valor-total">
                <span class="valor"><?php echo (float) $venda['total_venda']; ?></span><span class="simbolo"> € </span>
                <small>(Valor calculado na soma do pagamento em dinheiro e multibanco.)</small>
            </div>
        </div>
        <?php
        if (($venda['valor_multibanco_adiantado'] + $venda['valor_dinheiro_adiantado'] + $venda['valor_mbway_adiantado']) > 0) {
        ?>
            <div class="input-grupo input-camarote">
                <label for="input-nome">
                    Valor total camarote (€)
                </label>
                <div class="input valor-totalcamarote" data-adiantado="<?php echo (float) ($venda['valor_multibanco_adiantado'] + $venda['valor_dinheiro_adiantado'] + $venda['valor_mbway_adiantado']); ?>">
                    <span class="valor"><?php echo (float) $venda['total_venda'] + $venda['valor_dinheiro_adiantado'] + $venda['valor_multibanco_adiantado'] + $venda['valor_mbway_adiantado']; ?></span><span class="simbolo"> € </span>
                    <small>(Valor calculado na soma do pagamento adiantado com o pagamento da venda no dia do evento.)</small>
                </div>
            </div>

        <?php
        }
        ?>

        <div class="input-grupo">
            <div class="input">
                <input type="submit" value="Finalizar" />
            </div>
        </div>
    </form>
</div>