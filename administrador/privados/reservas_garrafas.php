<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1 && $tipo != 3 && $tipo != 4) {
    header('Location:/administrador/index.php');
    exit;
}

if ($data_evento == false) {
    if (date('H') < 12) {
        $data_evento = date('Y-m-d', strtotime('-1 day'));
    } else {
        $data_evento = date('Y-m-d');
    }
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);

$listas = $dbprivados->devolveListaEsperaGarrafas($data_evento);

?>
<h1 class="titulo"> Reservas de garrafas </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nome do cliente</th>
                    <th>Garrafas</th>
                    <th>Staff</th>
                    <th>Processado por</th>
                    <th>Total</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($listas)) {
                ?>
                    <td colspan="8">
                        Sem registos inseridos.
                    </td>
                <?php
                }
                foreach ($listas as $lista) {
                ?>
                    <tr>
                        <td><?php echo $lista['id']; ?></td>
                        <td><?php echo $lista['nome']; ?></td>
                        <td>
                            <?php 
                            foreach($lista['garrafas'] as $k => $garrafas){
                                if($k > 0){
                                    echo ", ";
                                }
                                $garrafa_array = $dbprivados ->devolveGarrafa($garrafas['id_garrafa']);
                                echo $garrafa_array['nome'];
                            } 
                            ?>
                        </td>
                        <td><?php echo $lista['nome_rp']; ?></td>
                        <td><?php echo $lista['nome_processado']; ?></td>
                        <td><?php echo euro($lista['valor']); ?></td>

                        <td class="text-nowrap">
                            <div class="opcoes">

                                <a href="?pg=inserir_venda_garrafas&id=0&id_reserva=<?php echo $lista['id']; ?>&data_evento=<?php echo $data_evento; ?>" class="entradas"> Efectuar compra </a>
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