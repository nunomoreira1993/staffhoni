<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$data_evento = $_GET['data'];
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
$caixas = $dbpagamentos->listaCaixasData($data_evento);
?>
<h1 class="titulo"> Caixas - <?php echo $data_evento; ?></h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Número de caixa</th>
                    <th>Total (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($caixas)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            foreach ($caixas as $caixa) {
                ?>
                    <tr>
                        <td><?php echo $caixa['numero']; ?></td>
                        <td><?php echo euro($caixa['valor']); ?></td>
                    </tr>
                <?php
            }
            ?>

            </tbody>
        </table>
    </div>
</div>