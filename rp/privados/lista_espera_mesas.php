<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/privados/privados.obj.php');
$dbprivados = new privados($db);
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

$lista = $dbprivados->devolveListaEspera($data_evento);

if ($_GET['apagar']) {
    $reserva = $dbprivados->devolveListaEspera($_GET['data_evento'], $_GET['id']);
    if ($permissao && $reserva) {
        $query = 'DELETE from venda_privados_lista_espera WHERE data_evento= "' . $_GET['data_evento'] . '" AND id="' . $_GET['id'] . '"';
        $db->query($query);
        $db->Insert('logs_rp', array('descricao' => "Apagou um cliente da lista de espera.", 'arr' => json_encode($reserva), 'id_rp' => $_SESSION['id_rp'], 'tipo' => "Apagar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        $_SESSION['sucesso'] = "Reserva cancelada com sucesso.";
    } else {
        $_SESSION['erro'] = "Não tem permissões para apagar este registo.";
    }
    header('Location: /rp/index.php?pg=lista_espera_mesas&data_evento=' . $_GET['data_evento']);
    exit;
}
?>
<div class="header">
    <h2>Lista de espera - <?php echo $data_evento; ?></h2>
</div>

<div class="conteudo disponibilidade" <?php echo escreveErroSucesso(); ?>>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="data formulario" method="get">
        <div class="inputs">
            <div class="label">
                Data do evento
            </div>
            <div class="input">
                <input type="hidden" name="pg" value="disponibilidade_de_mesas" />
                <input name="data_evento" value="<?php echo $data_evento; ?>" required="required" type="date" min="<?php echo date('Y-m-d', strtotime('-1 day')); ?>" />
            </div>
        </div>
    </form>
    <a href="/rp/index.php?pg=adicionar_lista_espera_mesa&id=0&data_evento=<?php echo $data_evento; ?>" class="adicionar">
        <span class="icon"> <img src="/temas/rps/imagens/adicionar.svg" /> </span>
        <span class="label"> Inserir cliente</span>
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
                                <?php echo $li['nome_cliente']; ?>
                            </div>
                        </div>
                        <div class="coluna">
                            <div class="titulo">
                                Nº Contacto
                            </div>
                            <div class="valor">
                                <?php echo $li['contacto']; ?>
                            </div>
                        </div>
                    </div>
                    <div class="topo">
                        <div class="coluna">
                            <div class="titulo">
                                Gerente
                            </div>
                            <div class="valor">
                                <?php echo $dbrp->devolveNomeRp($li['id_gerente']); ?>
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
                                Tipo de reserva
                            </div>
                            <div class="valor">
                                <?php echo ($li['tipo'] == 1 ? "Camarote" : "Mesa"); ?>
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
                    <div class="rodape">
                        <?php if ($li['id_gerente'] == $_SESSION['id_rp']) { ?>
                            <a href="/rp/index.php?pg=adicionar_lista_espera_mesa&data_evento=<?php echo $data_evento; ?>&id=<?php echo $li['id']; ?>" class="editar"> Editar </a>
                            <a href="/rp/index.php?pg=lista_espera_mesas&data_evento=<?php echo $data_evento; ?>&id=<?php echo $li['id']; ?>&apagar=1" class="apagar"> Apagar </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
    <?php
        }
    }
    ?>
</div>