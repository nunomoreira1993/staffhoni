<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/privados/privados.obj.php');
$dbprivados = new privados($db);
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);
$id_cargo = $dbrp->devolveCargo();

$nome_cliente = $_GET['nome_cliente'];

if ($_GET['data_evento']) {
    $data_evento = $_GET['data_evento'];
}

$salasMesasArr = $dbprivados->devolveSalasPesquisa($nome_cliente, $data_evento);
$mesasArr = $dbprivados->devolveMesasPesquisa($nome_cliente, $data_evento);

$salas = $dbprivados->listaSalas($salasMesasArr);

if ($_GET['cancelar']) {
    $reserva = $dbprivados->devolveReservaMesa($_GET['id_mesa'], $_GET['data_evento']);
    $mesa = $dbprivados->devolveMesa($_GET['id_mesa']);
    if ($permissao) {
        $query = 'DELETE from privados_salas_mesas_disponibilidade WHERE data_evento= "' . $_GET['data_evento'] . '" AND id_mesa="' . $_GET['id_mesa'] . '"';
        $db->query($query);
        $db->Insert('logs_rp', array('descricao' => "Cancelou a reserva de uma mesa.", 'arr' => json_encode($reserva), 'id_rp' => $_SESSION['id_rp'], 'tipo' => "Apagar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        $_SESSION['sucesso'] = "Reserva cancelada com sucesso.";
    } else {
        $_SESSION['erro'] = "Não tem permissões para cancelar esta reserva.";
    }
    header('Location: /rp/index.php?pg=disponibilidade_de_mesas&data_evento=' . $_GET['data_evento'] . '#sala_' . $mesa['id_sala']);
    exit;
}
?>
<div class="header">
    <h2>Disponibilidade de mesas</h2>
</div>

<div class="conteudo disponibilidade" <?php echo escreveErroSucesso(); ?>>
    <?php
    if (empty($salas)) {
        ?>
        <span class="sem_registos">
            Não foram encontrados registos.
        </span>
    <?php
    } else {
        ?>
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <?php
                    foreach ($salas as $sala) {
                        $mesas = $dbprivados->listaMesas($sala['id'], $mesasArr);
                        ?>
                        <div class="swiper-slide" id="sala_<?php echo $sala['id']; ?>" data-hash="sala_<?php echo $sala['id']; ?>">
                            <?php
                            if ($sala['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/privados/" . $sala['foto'])) {
                            ?>
                                <div class="hotspot">
                                    <img src="/fotos/privados/<?php echo $sala['foto']; ?>">
                                    <?php
                                    foreach ($mesas as $mesa) {
                                        if ($dbprivados->verificaMesaVendida($mesa['id'], $data_evento)) {
                                            $disponivel = 2;
                                        } else {
                                            if ($dbprivados->verificaMesaDisponivel($mesa['id'], $data_evento)) {
                                                $disponivel = 1;
                                            } else {
                                                $disponivel = 0;
                                            }
                                        }
                                        ?>
                                        <a class="mesa <?php if ($disponivel == 1) { ?> livre <?php } else if ($disponivel == 2) { ?> vendida <?php } else { ?> ocupada <?php } ?>" id="mesa_<?php echo $sala['id'] . "_" . $mesa['codigo_mesa']; ?>" <?php if ($permissao) { ?> href="/rp/index.php?pg=inserir_reserva&id=0&id_mesa=<?php echo $mesa['id']; ?>&data_evento=<?php echo $data_evento; ?>" <?php } ?>>
                                            <?php echo $mesa['codigo_mesa']; ?>
                                        </a>
                                    <?php
                                    }
                                    ?>
                                </div>
                            <?php
                            }
                            ?>
                        <div class="mesas">
                            <?php
                            foreach ($mesas as $mesa) {
                            ?>
                                <div class="mesa">
                                    <a class="topo">
                                        <span class="codigo">
                                            <?php echo $mesa['codigo_mesa']; ?>
                                        </span>
                                        <?php
										unset($vendas);
                                        if ($vendas = $dbprivados->verificaMesaVendida($mesa['id'], $data_evento)) {
                                        ?>
                                            <span class="estado vendida">
                                                Vendida
                                            </span>
                                        <?php
                                        }
                                        else {
                                            if ($dbprivados->verificaMesaDisponivel($mesa['id'], $data_evento)) {
                                                ?>
                                                    <span class="estado livre">
                                                        Não vendida
                                                    </span>
                                                <?php
                                            } else {
                                                ?>
                                                    <span class="estado ocupada">
                                                        Ocupado
                                                    </span>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </a>
                                    <?php
									if($vendas){
										foreach($vendas as $venda){
											?>
                                        <div class="info_reserva">
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Nº Venda
                                                </span>
                                                <span class="valor">
                                                    <?php echo $venda['id']; ?>
                                                </span>
                                            </div>
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Gerente
                                                </span>
                                                <span class="valor">
                                                    <?php echo $dbrp->devolveNomeRp($venda['id_gerente']); ?>
                                                </span>
                                            </div>
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Staff
                                                </span>
                                                <span class="valor">
                                                    <?php echo $dbrp->devolveNomeRp($venda['id_rp']); ?>
                                                </span>
                                            </div>
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Cliente
                                                </span>
                                                <span class="valor">
                                                    <?php echo $venda['nome_cliente']; ?>
                                                </span>
                                            </div>
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Data da reserva
                                                </span>
                                                <span class="valor">
                                                    <?php echo $venda['data_evento']; ?>
                                                </span>
                                            </div>

                                            <div class="bloco">
                                                <span class="titulo">
                                                    Nr. de Cartões
                                                </span>
                                                <span class="valor">
                                                    <?php echo $venda['numero_cartoes']; ?>
                                                </span>
                                            </div>
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Valor (€)
                                                </span>
                                                <span class="valor">
                                                    <?php echo euro($venda['total']); ?>
                                                </span>
                                            </div>
                                            <?php
                                            if($venda['valor_multibanco']){
                                            ?>
                                                <div class="bloco">
                                                    <span class="titulo">
                                                        Pago em Multibanco (€)
                                                    </span>
                                                    <span class="valor">
                                                        <?php echo euro($venda['valor_multibanco']); ?>
                                                    </span>
                                                </div>
                                            <?php
                                            }
                                            if ($venda['valor_dinheiro']) {
                                                ?>
                                                <div class="bloco">
                                                    <span class="titulo">
                                                        Pago em Dinheiro (€)
                                                    </span>
                                                    <span class="valor">
                                                        <?php echo euro($venda['valor_dinheiro']); ?>
                                                    </span>
                                                </div>
                                            <?php
                                            }
                                            if ($venda['valor_mbway']) {
                                                ?>
                                                <div class="bloco">
                                                    <span class="titulo">
                                                        Pago em MBWAY (€)
                                                    </span>
                                                    <span class="valor">
                                                        <?php echo euro($venda['valor_mbway']); ?>
                                                    </span>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
											<?php
										}
									}
									else if (!$dbprivados->verificaMesaDisponivel($mesa['id'], $data_evento)) {
                                        $reserva = $dbprivados->devolveReservaMesa($mesa['id'], $data_evento);
                                        ?>
                                        <div class="info_reserva">
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Gerente
                                                </span>
                                                <span class="valor">
                                                    <?php echo $dbrp->devolveNomeRp($reserva['id_gerente']); ?>
                                                </span>
                                            </div>
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Staff
                                                </span>
                                                <span class="valor">
                                                    <?php echo $dbrp->devolveNomeRp($reserva['id_rp']); ?>
                                                </span>
                                            </div>
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Cliente
                                                </span>
                                                <span class="valor">
                                                    <?php echo $reserva['nome']; ?>
                                                </span>
                                            </div>
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Data da reserva
                                                </span>
                                                <span class="valor">
                                                    <?php echo $reserva['data']; ?>
                                                </span>
                                            </div>

                                            <div class="bloco">
                                                <span class="titulo">
                                                    Nr. de garrafas
                                                </span>
                                                <span class="valor">
                                                    <?php echo $reserva['garrafas']; ?>
                                                </span>
                                            </div>
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Nr. de Cartões
                                                </span>
                                                <span class="valor">
                                                    <?php echo $reserva['cartoes']; ?>
                                                </span>
                                            </div>
                                            <div class="bloco">
                                                <span class="titulo">
                                                    Valor (€)
                                                </span>
                                                <span class="valor">
                                                    <?php echo euro($reserva['valor']); ?>
                                                </span>
                                            </div>
                                            <div class="bloco">
                                                <span class="titulo">
                                                    SMS Enviada
                                                </span>
                                                <span class="valor">
                                                    <?php echo ($reserva['sms_enviada']) ? "Sim" : "Não"; ?>
                                                </span>
                                            </div>
                                            <?php
                                            if($reserva['valor_multibanco_adiantado']){
                                            ?>
                                                <div class="bloco">
                                                    <span class="titulo">
                                                        Adiantado Multibanco (€)
                                                    </span>
                                                    <span class="valor">
                                                        <?php echo euro($reserva['valor_multibanco_adiantado']); ?>
                                                    </span>
                                                </div>
                                            <?php
                                            }
                                            if ($reserva['valor_dinheiro_adiantado']) {
                                                ?>
                                                <div class="bloco">
                                                    <span class="titulo">
                                                        Adiantado Dinheiro (€)
                                                    </span>
                                                    <span class="valor">
                                                        <?php echo euro($reserva['valor_dinheiro_adiantado']); ?>
                                                    </span>
                                                </div>
                                            <?php
                                            }
                                            if ($reserva['valor_mbway_adiantado']) {
                                                ?>
                                                <div class="bloco">
                                                    <span class="titulo">
                                                        Adiantado MBWAY (€)
                                                    </span>
                                                    <span class="valor">
                                                        <?php echo euro($reserva['valor_mbway_adiantado']); ?>
                                                    </span>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>

            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    <?php

    }
    ?>
</div>