<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

$data_evento = $_GET['data'];

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);
$vendas = $dbprivados->listaVendaGarrafas(false, $data_evento);
?>
<h1 class="titulo"> Vendas de garrafas em <?php echo $data_evento; ?> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nome do cliente</th>
                    <th>Staff</th>
                    <th>Processado por</th>
                    <th>Valor Multibanco</th>
                    <th>Valor Dinheiro</th>
                    <th>Total</th>
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
                        <td><?php echo $venda['nome_cliente']; ?></td>
                        <td><?php echo $venda['nome_rp']; ?></td>
                        <td><?php echo $venda['nome_processado']; ?></td>
                        <td><?php echo euro($venda['valor_multibanco']); ?></td>
                        <td><?php echo euro($venda['valor_dinheiro']); ?></td>
                        <td><?php echo euro($venda['total']); ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <?php
                                if ($tipo == 1) {
                                    ?>
                                    <a href="?pg=inserir_venda_garrafas&id=<?php echo $venda['id']; ?>&data_evento=<?php echo $data_evento; ?>" class="editar"> <i class="fa fa-pencil text-inverse m-r-10"></i> </a>
                                <?php
                                }
                                ?>
                                <a href="/administrador/privados/imprimir_compra_garrafa.php?id=<?php echo $venda['id']; ?>" class="entradas" target="_blank"> Imprimir </a>
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