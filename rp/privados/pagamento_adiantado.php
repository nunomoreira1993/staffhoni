<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);

require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/privados/privados.obj.php');
$dbprivados = new privados($db);

$permissao = $dbrp->permissao();

$rps = $dbrp->listaRps();

$reserva = $dbprivados->devolveReserva($_GET['id']);

if (empty($reserva)) {
    $_SESSION['erro'] = "A reserva que está a tentar efectuar o pagamento adiantado não foi encontrada.";
    header('Location: /rp/index.php?pg=disponibilidade_de_mesas&data_evento=' . $_GET['data_evento'] . '#sala_' . $mesa['id_sala']);
    exit;
} else {
    $nome = $reserva['nome'];
    $valor = $reserva['valor'];
    $valor_multibanco_adiantado = $reserva['valor_multibanco_adiantado'];
    $valor_dinheiro_adiantado = $reserva['valor_dinheiro_adiantado'];
    $valor_mbway_adiantado = $reserva['valor_mbway_adiantado'];
}

$mesa = $dbprivados->devolveMesa($_GET['id_mesa']);
if (empty($permissao)) {
    $_SESSION['erro'] = "Não tem permissão para aceder a esta página.";
    header('Location: /rp/index.php?pg=disponibilidade_de_mesas&data_evento=' . $_GET['data_evento'] . '#sala_' . $mesa['id_sala']);
    exit;
}

if (empty($mesa)) {
    $_SESSION['erro'] = "A mesa que está a tentar reserva não existe.";
    header('Location: /rp/index.php?pg=disponibilidade_de_mesas&data_evento=' . $_GET['data_evento'] . '#sala_' . $mesa['id_sala']);
    exit;
} else {
    $sala = $dbprivados->devolveSala($mesa['id_sala']);
}

if ($_GET['id_mesa']) {

    $cartao = $dbprivados->verificaMesaDisponivel(intval($_GET['id_mesa']), $_GET['data_evento']);

    if (!empty($cartao)) {
        $_SESSION['erro'] = "A mesa que está a tentar efectuar o pagamento antecipado ainda não tem reserva criada.";
        header('Location: /rp/index.php?pg=disponibilidade_de_mesas&data_evento=' . $_GET['data_evento'] . '#sala_' . $mesa['id_sala']);
        exit;
    }
}

if ($_POST) {
    $valor_multibanco_adiantado = $_POST['valor_multibanco_adiantado'];
    $valor_dinheiro_adiantado = $_POST['valor_dinheiro_adiantado'];
    $valor_mbway_adiantado = $_POST['valor_mbway_adiantado'];

    if (empty($_SESSION['erro'])) {
        $campos['valor_multibanco_adiantado'] = $valor_multibanco_adiantado;
        $campos['valor_dinheiro_adiantado'] = $valor_dinheiro_adiantado;
        $campos['valor_mbway_adiantado'] = $valor_mbway_adiantado;

        if ($_GET['id']) {
            $db->Update('privados_salas_mesas_disponibilidade', $campos, 'id=' . intval($_GET['id']));
            $_SESSION['sucesso'] = "O pagamento adiantado foi adicionado com sucesso.";

            $db->Insert('logs_rp', array('descricao' => "Alterou / Adicionou pagamento adiantado.", 'arr' => json_encode($campos), 'id_rp' => $_SESSION['id_rp'], 'tipo' => "alterar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        }

        header('Location: /rp/index.php?pg=disponibilidade_de_mesas&data_evento=' . $_GET['data_evento'] . '#sala_' . $mesa['id_sala']);
        exit;
    }
}
?>


<div class="header">
    <h2>Reservar mesa - <?php echo $mesa['codigo_mesa']; ?> - <?php echo $sala['nome']; ?> </h2>
</div>

<div class="conteudo" <?php echo escreveErroSucesso(); ?>>
    <a href="/rp/index.php?pg=disponibilidade_de_mesas&data_evento=<?php echo $_GET['data_evento']; ?>" class="voltar">
        <span class="icon"> <img src="/temas/rps/imagens/back.svg" /> </span>
        <span class="label"> Voltar </span>
    </a>

    <form name="formulario" class="consumo-obrigatorio" data-incremento="<?php echo count($campos['input']); ?>" action="" method="post">

        <div class="inputs">
            <div class="label">
                Data do evento
            </div>
            <div class="input">
                <?php echo $_GET['data_evento']; ?>
            </div>
        </div>

        <div class="inputs">
            <div class="label">
                Nome do cliente
            </div>
            <div class="input">
                <?php echo $nome; ?>
            </div>
        </div>

        <div class="inputs">
            <div class="label">
                Valor da reserva
            </div>
            <div class="input">
                <?php echo euro($valor); ?>
            </div>
        </div>

        <div class="inputs">
            <div class="label">
                Valor Multibanco (€)
            </div>
            <div class="input">
                <input name="valor_multibanco_adiantado" value="<?php echo $valor_multibanco_adiantado; ?>" type="number" step="0.01" />
            </div>
        </div>

        <div class="inputs">
            <div class="label">
                Valor Dinheiro (€)
            </div>
            <div class="input">
                <input name="valor_dinheiro_adiantado" value="<?php echo $valor_dinheiro_adiantado; ?>" type="number" step="0.01" />
            </div>
        </div>

        <div class="inputs">
            <div class="label">
                Valor MBWAY (€)
            </div>
            <div class="input">
                <input name="valor_mbway_adiantado" value="<?php echo $valor_mbway_adiantado; ?>" type="number" step="0.01" />
            </div>
        </div>


        <div class="inputs">
            <input type="submit" value="Enviar" />
        </div>
    </form>
</div>