<?php
if ($tipo != 1 && $tipo != 7 && $tipo != 3) {
    header('Location:/administrador/index.php');
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/privados/privados.obj.php');
$dbprivados = new privados($db);


require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);
$id_cargo = $dbrp->devolveCargo();


$salas_pesquisa = array();

if ($_GET['data_evento']) {
    $data_evento = $_GET['data_evento'];
} else {
    if (date('H') < 14) {
        $data_evento = date('Y-m-d', strtotime('-1 day'));
    } else {
        $data_evento = date('Y-m-d');
    }
}

if ($_POST['nome']) {
    $nome = $_POST['nome'];
    $mesas = $dbprivados->listaMesas(false, false, $nome, $data_evento);

    if ($mesas) {
        foreach ($mesas as $mesa) {
            $salas_pesquisa[] = $mesa['id_sala'];
            $mesas_pesquisa[] = $mesa['id_mesa'];
        }
    }
    if (empty($mesas)) {
        $_SESSION['erro'] = "Não foi encontrado nenhuma mesa com o nome do cliente indicado.";
        header('Location: /administrador/index.php?pg=entrada_privados');
        exit;
    }
}

$salas = $dbprivados->listaSalas($salas_pesquisa);

if ($_GET['remover']) {
    $reserva = $dbprivados->devolveOcupacaoMesa($_GET['id_mesa'], $data_evento);
    if ($reserva) {
        $mesa = $dbprivados->devolveMesa($_GET['id_mesa']);
        if ($mesa) {

            $query = 'DELETE from privados_salas_mesas_ocupacao WHERE data_evento= "' . $data_evento . '" AND id_mesa="' . $_GET['id_mesa'] . '"';
            $db->query($query);
            $estado = $db->Insert('logs', array('descricao' => "Apagou uma presença de privados para a mesa ID " . $_GET['id_mesa'], 'arr' => json_encode($campos), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "Apagar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));


            $_SESSION['sucesso'] = "Reserva libertada com sucesso.";
        } else {
            $_SESSION['erro'] = "Mesa não encontrada.";
        }
    } else {
        $_SESSION['erro'] = "Reserva não encontrada.";
    }
    header('Location: ?pg=entrada_privados#sala_' . $mesa['id_sala']);
    exit;
}
?>

<div class="content" id="entradas_disponibilidade" <?php echo escreveErroSucesso(); ?>>

    <form class="filtros" name="filtros" action="" method="POST">
        <div class="filtro">
            <span class="nome">Nome Cliente:</span>
            <span class="input"><input type="text" name="nome" value="<?php echo $_POST['nome']; ?>" /></span>
        </div>
        <input type="submit" value="Filtrar" />
        <a href="/administrador/index.php?pg=entrada_privados" class="clean"> Limpar filtros </a>
    </form>

    <div class="conteudo disponibilidade">
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
                        $mesas = $dbprivados->listaMesas($sala['id']);
                    ?>
                        <div class="swiper-slide" id="sala_<?php echo $sala['id']; ?>" data-hash="sala_<?php echo $sala['id']; ?>">
                            <?php
                            if ($sala['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/privados/" . $sala['foto'])) {
                            ?>
                                <div class="hotspot">
                                    <img src="/fotos/privados/<?php echo $sala['foto']; ?>">
                                    <?php
                                    foreach ($mesas as $mesa) {
                                        unset($disponivel);
                                        if ($mesas_pesquisa && in_array($mesa['id'], $mesas_pesquisa)) {
                                            $disponivel = 3;
                                        } else if ($dbprivados->verificaMesaOcupada($mesa['id'], $data_evento)) {
                                            $disponivel = 2;
                                        } else {
                                            if ($dbprivados->verificaMesaDisponivel($mesa['id'], $data_evento)) {
                                                $disponivel = 1;
                                            } else {
                                                $disponivel = 0;
                                            }
                                        }
                                    ?>
                                        <a class="mesa <?php if ($disponivel == 3) { ?> pesquisa ocupar <?php  } else if ($disponivel == 1) { ?> livre <?php } else if ($disponivel == 2) { ?> ocupado <?php } else { ?> ocupada <?php } ?> <?php if ($disponivel == 0) { ?> ocupar <?php } ?>" data-id="<?php echo $mesa['id']; ?>" id="mesa_<?php echo $sala['id'] . "_" . $mesa['codigo_mesa']; ?>" href="javascript:void(0);">
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
                                    if ($mesas_pesquisa) {
                                        if (!in_array($mesa['id'], $mesas_pesquisa)) {
                                            continue;
                                        }
                                    }
                                    $ocupada = $dbprivados->verificaMesaOcupada($mesa['id'], $data_evento);
                                    $disponivel = $dbprivados->verificaMesaDisponivel($mesa['id'], $data_evento);
                                ?>
                                    <div class="mesa">
                                        <a class="topo <?php if ($disponivel != 1 && $ocupada != 1) { ?> ocupar <?php } ?>" data-id="<?php echo $mesa['id']; ?>" href="javascript:void(0);">
                                            <span class="codigo">
                                                <?php echo $mesa['codigo_mesa']; ?>
                                            </span>
                                            <?php
                                            if ($ocupada) {
                                            ?>
                                                <span class="estado ocupado">
                                                    Ocupada
                                                </span>
                                                <?php
                                            } else {

                                                if ($disponivel) {
                                                ?>
                                                    <span class="estado livre">
                                                        Disponivel
                                                    </span>
                                                <?php
                                                } else {
                                                ?>
                                                    <span class="estado ocupada">
                                                        Reservada
                                                    </span>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </a>

                                        <?php

                                        $reserva = $dbprivados->devolveReservaMesa($mesa['id'], $data_evento);
                                        if ($ocupada) {
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
                                                        Nr. de Cartões de entrada
                                                    </span>
                                                    <span class="valor">
                                                        <?php echo $ocupada['cartoes']; ?>
                                                    </span>
                                                </div>
                                                <div class="botoes">
                                                    <a href="?pg=entrada_privados&id_mesa=<?php echo $mesa['id']; ?>&remover=1" class="cancelar">
                                                        Libertar mesa
                                                    </a>
                                                </div>
                                            </div>
                                        <?php
                                        } else if (!$dbprivados->verificaMesaDisponivel($mesa['id'], $data_evento)) {
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
                                                <?php
                                                if ($tipo == 1) {
                                                ?>
                                                    <div class="bloco">
                                                        <span class="titulo">
                                                            Valor (€)
                                                        </span>
                                                        <span class="valor">
                                                            <?php echo euro($reserva['valor']); ?>
                                                        </span>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                                <div class="botoes">
                                                    <a href="#" class="cancelar confirmar <?php if ($disponivel != 1 && $ocupada != 1) { ?> ocupar <?php } ?>" data-id="<?php echo $mesa['id']; ?>">
                                                        Confirmar Presença
                                                    </a>
                                                </div>
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