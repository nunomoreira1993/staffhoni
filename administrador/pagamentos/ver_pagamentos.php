<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$data_evento = $_GET['data'];

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
if ($_POST) {
    $filtro['nome'] = $_POST['nome'];
    $filtro['pagamento_caixa'] = $_POST['pagamento_caixa'];
    $filtro['id_administrador'] = $_POST['id_administrador'];
}
$pagamentos = $dbpagamentos->listaExcellPagamentosCaixa($data_evento, $filtro);
$filtros = $dbpagamentos->devolveFiltrosPagamentosCaixa($data_evento, $filtro);
if ($tipo == 1 && intval($_GET['apagar']) == 1) {

    $dbpagamentos->apagaContaCorrente(intval($_GET['id']));
    $_SESSION['sucesso'] = "O pagamento foi apagado com sucesso. Deverá adicionar novamente os extras no proximo pagamento.";
    header('Location: /administrador/index.php?pg=ver_pagamentos&data=' . $_GET['data']);
    exit;
}

?>

<h1 class="titulo"> Pagamentos - <?php echo $data_evento; ?> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>

    <a href="/administrador/exportar/exportar_linhas_pagamentos.php?data=<?php echo $data_evento; ?>" class="exportar-excell"> Exportar linhas de pagamentos </a>
    <form class="filtros" name="filtros" action="" method="post">
        <div class="filtro">
            <span class="nome">Nome:</span>
            <span class="input"><input type="text" name="nome" value="<?php echo $filtro['nome']; ?>" /></span>
        </div>
        <div class="filtro">
            <span class="nome">Caixa:</span>
            <span class="input">
                <select name="pagamento_caixa">

                    <?php
                    if (count($filtros['pagamento_caixa']) > 1) {
                        ?>
                        <option value=""> Escolha uma opção </option>
                    <?php
                }
                foreach ($filtros['pagamento_caixa'] as $pagamento_caixa) {
                    ?>
                        <option <?php if (strlen($filtro['pagamento_caixa']) > 0 && $filtro['pagamento_caixa'] == $pagamento_caixa['id']) { ?> selected="selected" <?php } ?> value="<?php echo $pagamento_caixa['pagamento_caixa']; ?>"> <?php echo $pagamento_caixa['nome']; ?> </option>
                    <?php
                }
                ?>
                </select>
            </span>
        </div>
        <div class="filtro">
            <span class="nome">Pago por:</span>
            <span class="input">
                <select name="id_administrador">
                    <?php
                    if (count($filtros['administradores']) > 1) {
                        ?>
                        <option value=""> Escolha uma opção </option>
                    <?php
                }
                foreach ($filtros['administradores'] as $administrador) {
                    ?>
                        <option <?php if (strlen($filtro['id_administrador']) > 0 && $filtro['id_administrador'] == $administrador['id']) { ?> selected="selected" <?php } ?> value="<?php echo $administrador['id']; ?>"> <?php echo $administrador['nome']; ?> </option>
                    <?php
                }
                ?>
                </select>
            </span>
        </div>
        <input type="submit" value="Filtrar" />
        <a href="/administrador/index.php?pg=ver_pagamentos&data=<?php echo $_GET['data']; ?>" class="clean"> Limpar filtros </a>

    </form>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo </th>
                    <th>Data</th>
                    <th>Total (€)</th>
                    <th>Caixa</th>
                    <th>Pago por:</th>
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
                $verifica_erro = $dbpagamentos->verificaErro($paga['id']);
                ?>
                    <tr <?php if ($paga['pagamento_caixa'] == 0) { ?>class="pagamento_caixa" <?php } ?>>
                        <td><?php echo $paga['nome']; ?></td>
                        <td><?php echo $paga['tipo']; ?></td>
                        <td><?php echo $paga['data']; ?></td>
                        <td class="<?php echo $paga['total'] < 0 ? "pago" : "recebido"; ?>">
                            <?php
                            if ($paga['total'] > 0) {
                                echo "<b> Pago </b>: " . euro($paga['total']);
                            } else if ($paga['total'] < 0) {
                                echo "<b> Dívida </b>: " . euro($paga['total']);
                            } else {
                                echo euro($paga['total']);
                            }
                            ?>
                        </td>
                        <td><?php echo $paga['pagamento_caixa'] == 1 ? "Sim" : "Não"; ?></td>
                        <td><?php echo $paga['nome_administrador']; ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <?php
                                if ($verifica_erro) {
                                    ?>
                                    <a class="fraude"> ERRO </a>
                                <?php
                            }
                            ?>
                                <a href="?pg=ver_pagamentos_detalhe&id=<?php echo $paga['id']; ?>" class="entradas"> Ver detalhe </a>
                                <?php
                                if ($tipo == 1) {
                                    ?>
                                    <a href="?pg=ver_pagamentos&apagar=1&id=<?php echo $paga['id']; ?>&data=<?php echo $_GET['data']; ?>" class="apagar"> <i class="fa fa-close text-danger"></i> </a>
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