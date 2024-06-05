<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);
$vendas = $dbprivados->listaVendaPrivados(true);
$permite_apagar = $dbprivados->permiteApagar();

if ($_GET['apagar'] == 1 && $_GET['id'] > 0) {
    $venda = $dbprivados->devolveVendaPrivados($_GET['id']);
    if ($venda) {
        $query = 'DELETE from venda_privados WHERE id=' . $_GET['id'];
        $db->query($query);
        $_SESSION['sucesso'] = "A compra foi apagada.";
        $db->Insert('logs', array('descricao' => "Apagou uma compra de privados", 'arr' => json_encode($rp), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "apagar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        header('Location: /administrador/index.php?pg=venda_privados');
        exit;
    }
}

?>
<h1 class="titulo"> Compra de privados <a href="?pg=escolher_mesa_privados"> Iniciar compra </a> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Área</th>
                    <th>Mesa</th>
                    <th>Nome do cliente</th>
                    <th>Gerente</th>
                    <th>Angariador</th>
                    <th>Processado por</th>
                    <th>Adiantado</th>
                    <th>Valor Multibanco</th>
                    <th>Valor Dinheiro</th>
                    <th>Valor MBWAY</th>
                    <th>Total</th>
                    <th>Total Camarote</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($vendas)) {
                    ?>
                    <td colspan="13">
                        Sem registos inseridos.
                    </td>
                <?php
                }
                foreach ($vendas as $venda) {

                    ?>
                    <tr>
                        <td><?php echo $venda['id']; ?></td>
                        <td><?php echo $venda['sala']; ?></td>
                        <td><?php echo $venda['mesa']; ?></td>
                        <td><?php echo $venda['nome_cliente']; ?></td>
                        <td><?php echo $venda['nome_gerente']; ?></td>
                        <td><?php echo $venda['nome_rp']; ?></td>
                        <td><?php echo $venda['nome_processado']; ?></td>
                        <td><?php echo euro($venda['valor_multibanco_adiantado'] + $venda['valor_dinheiro_adiantado'] + $venda['valor_mbway_adiantado']); ?></td>
                        <td><?php echo euro($venda['valor_multibanco']); ?></td>
                        <td><?php echo euro($venda['valor_dinheiro']); ?></td>
                        <td><?php echo euro($venda['valor_mbway']); ?></td>
                        <td><?php echo euro($venda['total_venda']); ?></td>
                        <td><?php echo euro($venda['total']); ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="" data-id="<?php echo $venda['id']; ?>" data-pago="<?php echo $venda['pago']; ?>" class="payment" ><?php echo $venda['pago'] == 1 ? "Pago" : "Não Pago"; ?> </a>
                                <a href="/administrador/privados/imprimir_compra_privados.php?id=<?php echo $venda['id']; ?>" class="entradas" target="_blank"> Imprimir </a>

                                <?php
                                    if ($permite_apagar) {
                                        ?>
                                    <a href="?pg=venda_privados&apagar=1&id=<?php echo $venda['id']; ?>" class="apagar"> <i class="fa fa-close text-danger"></i> </a>
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