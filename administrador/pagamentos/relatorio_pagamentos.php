<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);

if ($_SESSION['id_utilizador'] != 18 && $_SESSION['id_utilizador'] != 19 && $_SESSION['id_utilizador'] != 10 && $_SESSION['id_utilizador'] != 1) {
    if (date('H') < 14) {
        $data = date('Y-m-d', strtotime('-1 day'));
    } else {
        $data = date('Y-m-d');
    }
}

$pagamentos = $dbpagamentos->listaPagamentosCaixaData($data);
?>
<h1 class="titulo"> Relatório - Pagamentos </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Data Pagamento</th>
                    <th>Total Caixas (€)
                        <br /> <small>(Soma de todas as caixas)</small></th>
                    <th>Total P/(caixa) (€)
                        <br /> <small>(Valor que o staff recebeu em caixa)</small>
                    </th>
                    <th>Diferença <br /> <small>(T/Caixa - T/Pago)</small> </th>
                    <th>Total P/(Fora d/Caixa) (€)
                        <br /> <small>(Valor que não contabilizou para a caixa)</small>
                    </th>

                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($pagamentos)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            foreach ($pagamentos as $paga) {
                ?>
                    <tr>
                        <td><?php echo $paga['data']; ?></td>
                        <td class="recebido"><?php echo euro($paga['total_caixa']); ?></td>
                        <td class="pago"><?php echo euro($paga['total_pagamentos']); ?></td>
                        <td class="<?php echo $paga['diferenca'] > 0 ? "recebido" : "pago"; ?>">
                            <?php echo euro($paga['diferenca']); ?>
                        </td>
                        <td class="pago"><?php echo euro($paga['total_pagamentos_scaixa']); ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="/administrador/exportar/exportar_pagamentos_caixa.php?data=<?php echo $paga['data']; ?>" class="exportar-excell"> Exportar </a>
                                <a href="?pg=ver_pagamentos&data=<?php echo $paga['data']; ?>" class="entradas"> Pagamentos </a>
                                <a href="?pg=valores_caixa&data=<?php echo $paga['data']; ?>" class="entradas"> Valores caixa </a>
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