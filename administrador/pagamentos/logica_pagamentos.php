<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1 && $tipo != 3) {
    header('Location:/administrador/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);

$logica_pagamentos = $dbpagamentos->listaLogicaPagamentos();


if ($_GET['apagar'] == 1 && $_GET['id'] > 0) {
    $logica_pagamento = $dbpagamentos->devolveLogicaPagamentos($_GET['id']);
    if ($logica_pagamento) {
        $query = 'DELETE from logica_pagamentos WHERE id=' . $_GET['id'];
        $db->query($query);
        $_SESSION['sucesso'] = "O registo de lógica de pagamentos foi apagado.";
        $db->Insert('logs', array('descricao' => "Apagou um registo de lógica de pagamentos", 'arr' => json_encode($rp), 'id_admin' => $_SESSION['id_utilizador'], 'tipo' => "apagar", 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'ip' => $_SERVER['REMOTE_ADDR']));
        header('Location: /administrador/index.php?pg=logica_pagamentos');
        exit;
    }
}

?>
<h1 class="titulo"> Lógica de pagamentos <a href="?pg=inserir_logica_pagamentos&id=0"> Criar nova linha </a> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>De</th>
                    <th>Até</th>
                    <th>Valor (€)</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($logica_pagamentos)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php
            }
            foreach ($logica_pagamentos as $logica) {
                ?>
                    <tr>
                        <td><?php echo $logica['de']; ?></td>
                        <td><?php echo $logica['ate']; ?></td>
                        <td><?php echo euro($logica['valor']); ?></td>
                        </td>

                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="?pg=inserir_logica_pagamentos&id=<?php echo $logica['id']; ?>" class="editar"> <i class="fa fa-pencil text-inverse m-r-10"></i> </a>
                                <a href="?pg=logica_pagamentos&apagar=1&id=<?php echo $logica['id']; ?>" class="apagar"> <i class="fa fa-close text-danger"></i> </a>
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