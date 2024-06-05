<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

$data_evento = $_GET['data'];

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);
$vendas = $dbprivados->listaVendaPrivados(true, $data_evento);

?>
<h1 class="titulo"> Vendas de privados em <?php echo $data_evento; ?> </h1>
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
                    <th>Staff</th>
                    <th>Processado por</th>
                    <th>Adi. Multibanco</th>
                    <th>Adi. Dinheiro</th>
                    <th>Adi. MBWay</th>
                    <th>Valor Multibanco </th>
                    <th>Valor Dinheiro</th>
                    <th>Valor MBWay</th>
                    <th>Total</th>
                    <th>Total Camarote</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($vendas)) {
                    ?>
                    <td colspan="10">
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
                        <td><?php echo euro($venda['valor_multibanco_adiantado']); ?></td>
                        <td><?php echo euro($venda['valor_dinheiro_adiantado']); ?></td>
                        <td><?php echo euro($venda['valor_mbway_adiantado']); ?></td>
                        <td><?php echo euro($venda['valor_multibanco']); ?></td>
                        <td><?php echo euro($venda['valor_dinheiro']); ?></td>
                        <td><?php echo euro($venda['valor_mbway']); ?></td>
                        <td><?php echo euro($venda['total_venda']); ?></td>
                        <td><?php echo euro($venda['total']); ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="/administrador/privados/imprimir_compra_privados.php?id=<?php echo $venda['id']; ?>" class="entradas" target="_blank"> Imprimir </a>
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