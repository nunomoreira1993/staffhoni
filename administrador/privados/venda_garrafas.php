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
$vendas = $dbprivados->listaVendaGarrafas(true);
$permite_apagar = $dbprivados->permiteApagar();

if ($_GET['apagar'] == 1 && $_GET['id'] > 0) {
    $venda = $dbprivados->devolveVendaGarrafas($_GET['id']);
    if ($venda) {
        $query = 'DELETE from venda_garrafas_bar WHERE id=' . $_GET['id'];
        $db->query($query);
        $_SESSION['sucesso'] = "A compra foi apagada.";
        $db->Insert('logs', array('descricao' => "Apagou uma compra de garrafa", 'arr' => json_encode($rp), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "apagar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        header('Location: /administrador/index.php?pg=venda_garrafas');
        exit;
    }
}

?>
<h1 class="titulo"> Compra de garrafas <a href="?pg=inserir_venda_garrafas&id=0"> Iniciar compra </a> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nome do cliente</th>
                    <th>Angariador</th>
                    <th>Processado por</th>
                    <th>Valor Multibanco</th>
                    <th>Valor Dinheiro</th>
                    <th>Valor MBWAY</th>
                    <th>Total</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($vendas)) {
                    ?>
                    <td colspan="8">
                        Sem registos inseridos.
                    </td>
                <?php
            }
            foreach ($vendas as $venda) {
                ?>
                    <tr>
                        <td><?php echo $venda['id']; ?></td>
                        <td><?php echo $venda['nome_cliente']; ?></td>
                        <td><?php echo $venda['nome_rp']; ?></td>
                        <td><?php echo $venda['nome_processado']; ?></td>
                        <td><?php echo euro($venda['valor_multibanco']); ?></td>
                        <td><?php echo euro($venda['valor_dinheiro']); ?></td>
                        <td><?php echo euro($venda['valor_mbway']); ?></td>
                        <td><?php echo euro($venda['total']); ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">

                                <a href="/administrador/privados/imprimir_compra_garrafa.php?id=<?php echo $venda['id']; ?>" class="entradas" target="_blank"> Imprimir </a>
                                <?php
                                if ($tipo == 1) {
                                    ?>
                                    <a href="?pg=inserir_venda_garrafas&id=<?php echo $venda['id']; ?>" class="editar"> <i class="fa fa-pencil text-inverse m-r-10"></i> </a>
                                <?php
                                }
                                ?>

                                <?php
                                if ($permite_apagar) {
                                    ?>
                                    <a href="?pg=venda_garrafas&apagar=1&id=<?php echo $venda['id']; ?>" class="apagar"> <i class="fa fa-close text-danger"></i> </a>
                                <?php
                            }
                            ?>
                            </div>
                        </td>
                    </tr>
                <?php
            }
            ?>

            </tbody>
        </table>
    </div>
</div>