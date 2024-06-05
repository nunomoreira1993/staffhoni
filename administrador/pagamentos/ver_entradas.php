<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$data_evento = $_GET['data'];

if ($_POST) {
    $filtro['nome'] = $_POST['nome'];
    $filtro['data_de'] = $_POST['data_de'];
    $filtro['data_ate'] = $_POST['data_ate'];
    $filtro['numero_cartao'] = $_POST['numero_cartao'];
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
$entradas = $dbpagamentos->listaPresencasEventosData($data_evento, $filtro);
?>

<h1 class="titulo"> Entradas Staff - <?php echo $data_evento; ?> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>

    <form class="filtros" name="filtros" action="" method="post">
        <div class="filtro">
            <span class="nome">Nome:</span>
            <span class="input"><input type="text" name="nome" value="<?php echo $filtro['nome']; ?>" /></span>
        </div>
        <div class="filtro">
            <span class="nome">Data (de):</span>
            <span class="input">
                <input type="datetime-local" name="data_de" value="<?php echo $filtro['data_de']; ?>" />
            </span>
        </div>
        <div class="filtro">
            <span class="nome">Data (até):</span>
            <span class="input">
                <input type="datetime-local" name="data_ate" value="<?php echo $filtro['data_ate']; ?>" />
            </span>
        </div>
        <div class="filtro">
            <span class="nome">Número Cartão:</span>
            <span class="input"><input type="number" name="numero_cartao" value="<?php echo $filtro['numero_cartao']; ?>" /></span>
        </div>
        <input type="submit" value="Filtrar" />
        <a href="/administrador/index.php?pg=ver_entradas&data=<?php echo $data_evento; ?>" class="clean"> Limpar filtros </a>

    </form>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Data de entrada </th>
                    <th>Número de cartão</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($entradas)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            foreach ($entradas as $entrada) {
                ?>
                    <tr>
                        <td><?php echo $entrada['nome']; ?></td>
                        <td><?php echo $entrada['data_entrada']; ?></td>
                        <td><?php echo $entrada['numero_cartao']; ?></td>
                    </tr>
                <?php
            }
            ?>

            </tbody>
        </table>
    </div>
</div>