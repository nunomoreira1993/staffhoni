<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
$data_evento = $_GET['data'];

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
$convites = $dbpagamentos->listaConvitesEventosData($data_evento);
?>

<h1 class="titulo"> Convites - <?php echo $data_evento; ?> </h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Data de Submissão </th>
                    <th>Imagem</th>
                    <th class="text-nowrap"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($convites)) {
                    ?>
                    <td colspan="5">
                        Sem registos inseridos.
                    </td>
                <?php

            }
            foreach ($convites as $convite) {
                ?>
                    <tr>
                        <td><?php echo $convite['nome']; ?></td>
                        <td><?php echo $convite['data']; ?></td>
                        <td>
                            <a href="/fotos/convites/<?php echo $convite['imagem']; ?>" href="/fotos/convites/<?php echo $convite['imagem']; ?>" data-fancybox data-caption="Convite para evento <?php echo $data_evento; ?>" class="imagem">
                                <img src="/fotos/convites/<?php echo $convite['imagem']; ?>" height="200px" />
                                <br />
                                <small> Clicar na foto para ver ampliada </small>
                            </a>
                        </td>
                        <td class="text-nowrap">
                            <div class="opcoes">
                                <a href="?pg=editar_convite&id=<?php echo $convite['id']; ?>" class="editar"> <i class="fa fa-pencil text-inverse m-r-10"></i> </a>
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