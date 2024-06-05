<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);

require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/privados/privados.obj.php');
$dbprivados = new privados($db);

$permissao = $dbrp->permissao();

$rps = $dbrp->listaRps();
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

    if (empty($cartao)) {
        $_SESSION['erro'] = "A mesa que está a tentar reserva já não se encontra com disponibilidade.";
        header('Location: /rp/index.php?pg=disponibilidade_de_mesas&data_evento=' . $_GET['data_evento'] . '#sala_' . $mesa['id_sala']);
        exit;
    }
}
$mensagem_default = $dbprivados->getMensagemDefault();

$campos = array();

if ($_POST) {
    $id_gerente = $_SESSION['id_rp'];
    $id_rp = $_POST['id_rp'];
    $nome_cliente = $_POST['nome'];
    $nome_array = explode(" ", $nome_cliente);
    $data_evento = $_GET['data_evento'];
    $id_mesa = $_GET['id_mesa'];
    $garrafas = $_POST['garrafas'];
    $cartoes = $_POST['cartoes'];
    $valor = $_POST['valor'];
    $mensagem = trim(str_replace("{VALOR}", ($valor / 2) , $_POST['mensagem']));
    $mensagem = trim(str_replace("{NOME}", ($nome_array[0]) , $mensagem));
    $mensagem = trim(str_replace("{DATA}", (date('d/m/Y', strtotime($data_evento))), $mensagem));
    $telemovel = $_POST['telemovel'];


    if (empty($data_evento)) {
        $_SESSION['erro'] = "Por favor introduza a data do evento.";
    }
    if (empty($id_rp)) {
        $_SESSION['erro'] = "Por favor escolha um elemento do Staff para associar á reserva.";
    }
    if (empty($nome_cliente)) {
        $_SESSION['erro'] = "Por favor introduza o nome do cliente.";
    }
    if (empty($id_mesa)) {
        $_SESSION['erro'] = "Não foi possivel inserir a reserva porque deve escolher uma mesa.";
    }
    if (empty($id_gerente)) {
        $_SESSION['erro'] = "Não foi possivel inserir a reserva porque existe agente.";
    }

    if (strlen($telemovel) > 8 && $mensagem && empty($sms_enviada)) {
        $sms = $dbprivados->smsto(array('telemovel' => str_replace("+", "", $telemovel), 'mensagem' => $mensagem));

        if($sms->error){
            $campos['sms_erro'] = 1;
            $campos['sms_erro_mensagem'] = $sms->error->code." - ". $sms->error->description;
            $campos['sms_data_erro'] = date('Y-m-d H:i:s');
        }
        else if($sms[0]->accepted){
            $campos['sms_enviada'] = 1;
            $campos['sms_id'] = $sms[0]->id;
            $campos['sms_data_envio'] = date('Y-m-d H:i:s');
        }
        else{
            $campos['sms_erro'] = 1;
            $campos['sms_erro_mensagem'] = "O Serviço de mensagens não se encontra disponivel. Mandar mensagem manualmente.";
        }
    }

    if (empty($_SESSION['erro'])) {
        $campos['data'] = date('Y-m-d H:i:s');
        $campos['data_evento'] = $data_evento;
        $campos['id_mesa'] = $id_mesa;
        $campos['id_rp'] = $id_rp;
        $campos['id_gerente'] = $id_gerente;
        $campos['nome'] = $nome_cliente;
        $campos['garrafas'] = $garrafas;
        $campos['cartoes'] = $cartoes;
        $campos['valor'] = $valor;
        $campos['mensagem'] = $mensagem;
        $campos['telemovel'] = $telemovel;


        if ($_GET['id']) {
            $db->Update('privados_salas_mesas_disponibilidade', $campos, 'id=' . intval($_GET['id']));

            if ($campos['sms_erro'] == 1) {
                $_SESSION['erro'] = "Reserva criada mas houve um erro a enviar a SMS: ". $campos['sms_erro_mensagem'];
            }
            else{
                $_SESSION['sucesso'] = "A reserva foi alterada.";
            }

        } else {
            $id = $db->Insert('privados_salas_mesas_disponibilidade', $campos);
            if ($campos['sms_erro'] == 1) {
                $_SESSION['erro'] = "Reserva criada mas houve um erro a enviar a SMS: ". $campos['sms_erro_mensagem'];
            }
            else{
                $_SESSION['sucesso'] = "A reserva foi criada";
            }
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

    <form name="formulario" class="consumo-obrigatorio" action="" method="post">
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
                <input name="nome" value="<?php echo $nome; ?>" type="text" required="required" />
            </div>
        </div>

        <div class="inputs">
            <div class="label">
                Staff
            </div>
            <div class="input">
                <select name="id_rp" required="required">
                    <option value="">
                        Selecione um Staff
                    </option>
                    <?php
                    foreach ($rps as $rp) {
                    ?>
                        <option value="<?php echo $rp['id']; ?>" <?php if ($id_rp == $rp['id']) { ?> selected="selected" <?php } ?>><?php echo $rp['nome']; ?> </option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="inputs">
            <div class="label">
                Número de garrafas
            </div>
            <div class="input">
                <input name="garrafas" value="<?php echo $garrafas; ?>" type="number" />
            </div>
        </div>

        <div class="inputs">
            <div class="label">
                Número de cartões
            </div>
            <div class="input">
                <input name="cartoes" value="<?php echo $cartoes; ?>" type="number" />
            </div>
        </div>

        <div class="inputs">
            <div class="label">
                Valor (€)
            </div>
            <div class="input">
                <input name="valor" value="<?php echo $valor; ?>" type="number" step="0.01" />
            </div>
        </div>

        <div class="inputs">
            <div class="label">
                Nº de Telemovel
            </div>
            <div class="input">
                <input name="telemovel" value="<?php echo $telemovel?$telemovel:"+351"; ?>" type="tel" step="0.01" />
            </div>
        </div>


        <div class="inputs">
            <input type="submit" value="Enviar" />
        </div>


        <div class="inputs">
            <div class="label">
                Pre-visalização de mensagem
            </div>
            <div class="input">
                <textarea name="mensagem"><?php echo $mensagem_default;?></textarea>
            </div>
        </div>
    </form>
</div>