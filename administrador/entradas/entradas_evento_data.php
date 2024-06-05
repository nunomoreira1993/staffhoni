<?php 
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$data = $_GET['data'];
if (empty($data)) {
    header('Location:/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$gerir_entradas = $dbrps->listaEntradasData($data);
?>
<h1 class="titulo"> Entradas em <?php echo $data; ?> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Nome RP</th>
                    <th>Data</th>
                    <th>Quantidade</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (empty($gerir_entradas)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php
                }
                foreach ($gerir_entradas as $entradas) {
                ?>
                    <tr>
                        <td><?php echo $entradas['nome']; ?></td>
                        <td><?php echo $entradas['data']; ?></td>
                        <td><?php echo $entradas['quantidade']; ?></td>
                    </tr>
                <?php 
                }
                ?>
            </tbody>
        </table>
    </div>
</div>