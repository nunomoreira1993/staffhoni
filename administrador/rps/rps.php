<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1) {
    header('Location:/administrador/index.php');
    exit;
}

$pagina = intval($_GET['p']);

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);

$quantidade = 20;
$limit = devolveLimit(array('pagina' => $pagina, 'numero' => $quantidade));

if ($_GET['nome'] || $_GET['telemovel']) {
    $filtro['nome'] = $_GET['nome'];
    $filtro['telemovel'] = $_GET['telemovel'];
}

$rps = $dbrps->listaRPs(false, false, false, false,  $filtro, $limit);
$numeroRps = $dbrps->listaNumeroRPS(false, false, false, false, $filtro);

if ($_GET['apagar'] == 1 && $_GET['id'] > 0) {
    $rp = $dbrps->devolveRP($_GET['id']);
    if ($rp) {
        if ($rp['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/rps/" . $rp['foto'])) {
            var_dump(unlink($_SERVER['DOCUMENT_ROOT'] . "/fotos/rps/" . $rp['foto']));
        }
        $query = 'DELETE from rps WHERE id=' . $_GET['id'];
        $db->query($query);
        $_SESSION['sucesso'] = "O RP foi apagado.";
        $db->Insert('logs', array('descricao' => "Apagou um RP", 'arr' => json_encode($rp), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "apagar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        header('Location: /administrador/index.php?pg=rps');
        exit;
    }
}

?>
<h1 class="titulo"> Staff <a href="?pg=inserir_rp&id=0"> Criar novo </a> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form class="filtros" name="filtros" action="" method="GET">
        <input type="hidden" name="pg" value="<?php echo $pg; ?>"/>
        <input type="hidden" name="p" value="<?php echo $p<1?1:$p; ?>"/>
        <div class="filtro">
            <span class="nome">Nome:</span>
            <span class="input"><input type="text" name="nome" value="<?php echo $filtro['nome']; ?>" /></span>
        </div>
        <div class="filtro">
            <span class="nome">Telemovel:</span>
            <span class="input"><input type="text" name="telemovel" value="<?php echo $filtro['telemovel']; ?>" /></span>
        </div>
        <input type="submit" value="Filtrar" />
        <a href="/administrador/index.php?pg=rps&p=<?php echo $_GET['p']; ?>" class="clean"> Limpar filtros </a>
    </form>

    <div style="margin-bottom:20px" class="filtros">
        <span class="registos"> Foram encontrados <b> <?php echo $numeroRps; ?> </b> registos. </span>
    </div>

    <?php
    echo devolvePaginacao($pagina, $numeroRps, $quantidade);
    ?>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telemóvel</th>
                    <th>Foto</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($rps)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            foreach ($rps as $rpp) {
                ?>
                    <tr>
                        <td><?php echo $rpp['nome']; ?></td>
                        <td><?php echo $rpp['email']; ?></td>
                        <td><?php echo $rpp['telemovel']; ?></td>
                        <td>
                            <?php
                            if ($rpp['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/rps/" . $rpp['foto'])) {
                                ?>
                                <img src="/fotos/rps/<?php echo $rpp['foto']; ?>" width="60px">
                            <?php
                        }
                        ?>

                        </td>
                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="?pg=entradas_rp&id=<?php echo $rpp['id']; ?>" class="entradas"> Entradas </a>
                                <a href="?pg=inserir_rp&id=<?php echo $rpp['id']; ?>" class="editar"> <i class="fa fa-pencil text-inverse m-r-10"></i> </a>
                                <a href="?pg=rps&apagar=1&id=<?php echo $rpp['id']; ?>" class="apagar"> <i class="fa fa-close text-danger"></i> </a>
                            </div>
                        </td>
                    </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
    echo devolvePaginacao($pagina, $numeroRps, $quantidade);
    ?>
</div>