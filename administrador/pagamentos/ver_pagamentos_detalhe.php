<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$id_conta_corrente = $_GET['id'];

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
$linhas = $dbpagamentos->listaLinhasContaCorrente($id_conta_corrente);
$conta_corrente = $dbpagamentos->devolveContaCorrenteID($id_conta_corrente);

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$rp = $dbrps->devolveRP($conta_corrente['id_rp']);

?>

<h1 class="titulo"> Pagamento de <b> <?php echo $rp['nome']; ?> </b> em <b><?php echo $conta_corrente['data']; ?> </b></h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição </th>
                    <th>Valor (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($linhas)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            $total = 0;
            foreach ($linhas as $linha) {
                $total += $linha['valor']
                ?>
                    <tr>
                        <td><?php echo $linha['nome']; ?></td>
                        <td><?php echo $linha['descricao']; ?></td>
                        <td>
                            <?php
                            echo euro($linha['valor']);
                            ?>
                        </td>
                    </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="total_pagamento">
        <div class="titulo">
            <?php echo $conta_corrente['total'] < 0 ? "<b> Valor Recebido: </b>" : "<b> Valor pago:</b>"; ?>
        </div>
        <div class="valor <?php echo $conta_corrente['total'] < 0 ? "recebido" : "pago"; ?>">
            <?php echo $conta_corrente['total'] < 0 ? euro($conta_corrente['total']) : euro($conta_corrente['total']); ?>
        </div>
    </div>
</div>