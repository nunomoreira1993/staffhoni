<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/privados/privados.obj.php');
$dbprivados = new privados($db);


$_SESSION['id_processado'] = $_SESSION['id_rp'];
$_SESSION['id_utilizador'] = $dbprivados->devolveIDPrivado();

require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);
$id_cargo = $dbrp->devolveCargo();
$permissao = $dbrp->permissao();
if ($_GET['data_evento']) {
    $data_evento = $_GET['data_evento'];
} else {
    $proximo_privado = $dbprivados->devolveProximoPrivado();
    if (empty($proximo_privado)) {
        if (date('H') < 14) {
            $data_evento = date('Y-m-d', strtotime('-1 day'));
        } else {
            $data_evento = date('Y-m-d');
        }
    } else {
        $data_evento = $proximo_privado['data_evento'];
    }
}

$lista = $dbprivados->devolveListaEsperaGarrafas($data_evento);

if ($_GET['apagar']) {
    $reserva = $dbprivados->devolveListaEsperaGarrafas($_GET['data_evento'], $_GET['id']);

    if ($permissao && $reserva) {
        $query = 'DELETE from reserva_garrafas WHERE data_evento= "' . $_GET['data_evento'] . '" AND id="' . $_GET['id'] . '"';
        $db->query($query);
        $query = 'DELETE from reserva_garrafas_garafas WHERE id_reserva ="' . $_GET['id'] . '"';
        $db->query($query);
        $db->Insert('logs_rp', array('descricao' => "Apagou uma reserva de garrafas.", 'arr' => json_encode($reserva), 'id_rp' => $_SESSION['id_rp'], 'tipo' => "Apagar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        $_SESSION['sucesso'] = "Reserva cancelada com sucesso.";
    } else {
        $_SESSION['erro'] = "Não tem permissões para apagar este registo.";
    }
    header('Location: /rp/index.php?pg=reserva_garrafas&data_evento=' . $_GET['data_evento']);
    exit;
}
?>
<div class="header">
    <h2>Reserva de garrafas - <?php echo $data_evento; ?></h2>
</div>

<div class="conteudo disponibilidade" <?php echo escreveErroSucesso(); ?>>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="data formulario" method="get">
        <div class="inputs">
            <div class="label">
                Data do evento
            </div>
            <div class="input">
                <input type="hidden" name="pg" value="reserva_garrafas" />
                <input name="data_evento" value="<?php echo $data_evento; ?>" required="required" type="date" min="<?php echo date('Y-m-d', strtotime('-1 day')); ?>" />
            </div>
        </div>
    </form>
    <a href="/rp/index.php?pg=adicionar_reserva_garrafa&id=0&data_evento=<?php echo $data_evento; ?>" class="adicionar">
        <span class="icon"> <img src="/temas/rps/imagens/adicionar.svg" /> </span>
        <span class="label"> Inserir Garrafa</span>
    </a>


    <?php
    if (empty($lista)) {
        ?>
        <span class="sem_registos">
            Não foram encontrados registos.
        </span>
        <?php
        } else {
            foreach ($lista as $li) {
            ?>
            <div class="tabela lista">
                <div class="item">
                    <div class="topo">
                        <div class="coluna">
                            <div class="titulo">
                                Nome Cliente
                            </div>
                            <div class="valor">
                                <?php echo $li['nome']; ?>
                            </div>
                        </div>
                        <div class="coluna">
                            <div class="titulo">
                                Staff
                            </div>
                            <div class="valor">
                                <?php echo $dbrp->devolveNomeRp($li['id_rp']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="topo">
                        <div class="coluna">
                            <div class="titulo">
                                Valor
                            </div>
                            <div class="valor">
                                <?php echo ($li['valor']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="topo">

                        <div class="coluna">
                            <div class="titulo">
                                Staff Reserva
                            </div>
                            <div class="valor">
                                <?php echo $dbrp->devolveNomeRp($li['id_processado']); ?>
                            </div>
                        </div>
                        <div class="coluna">
                            <div class="titulo">
                                Adicionado a:
                            </div>
                            <div class="valor">
                                <?php echo ($li['data']); ?>
                            </div>
                        </div>
                    </div>
                
                    <?php 
                    if ($li['id_processado'] == $_SESSION['id_rp']) { 
                    ?>
                        <a href="/administrador/?pg=inserir_venda_garrafas&id=0&id_reserva=<?php echo $li['id']; ?>&data_evento=<?php echo $data_evento; ?>" class="pagamento">
                            Efectuar compra
                        </a>
                    <?php
                    }
                    ?>
                    <div class="rodape">
                        <?php if ($li['id_processado'] == $_SESSION['id_rp']) { ?>
                            <a href="/rp/index.php?pg=adicionar_reserva_garrafa&data_evento=<?php echo $data_evento; ?>&id=<?php echo $li['id']; ?>" class="editar"> Editar </a>
                            <a href="/rp/index.php?pg=reserva_garrafas&data_evento=<?php echo $data_evento; ?>&id=<?php echo $li['id']; ?>&apagar=1" class="apagar"> Apagar </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
    <?php
        }
    }
    ?>
</div>