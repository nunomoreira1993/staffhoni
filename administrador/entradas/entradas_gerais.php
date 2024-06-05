<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

if($_GET['data']){
    $data = $_GET['data'];
}
else{
    if (date('H') < 14) {
        $data = date('Y-m-d', strtotime('-1 day'));
    } else {
        $data = date('Y-m-d');
    }
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
$dbrps = new rps($db);
$entradas_array = $dbrps->listaEntradasGeraisDia($data);

if ($_POST) {
    $array_insert['id_rp'] = 0;
    $array_insert['quantidade'] = (int) $_POST['entradas'];
    $array_insert['data_evento'] = $data;
    $array_insert['data'] = date('Y-m-d H:i:s');
    if ($entradas_array['id']) {
        $db->Update('rps_entradas', $array_insert, 'id=' . $entradas_array['id']);
    } else {
        $db->Insert('rps_entradas', $array_insert);
    }

    $_SESSION['sucesso'] = "Número de entradas registado para o dia " . $data;
    header('Location: /administrador/index.php?pg=entradas_gerais&data='.$data);
    exit;
}
?>

<h1 class="titulo">Adicionar Entradas Gerais - <?php echo $data; ?></h1>
<div class="content" <?php echo escreveErroSucesso(); ?>>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="input-grupo">
            <label for="input-entradas">
                Número de entradas
            </label>
            <div class="input">
                <input type="number" value="<?php echo $entradas_array['total']; ?>" name="entradas" id="input-entradas" placeholder="Número de entradas" autocomplete="new-password" />
            </div>
        </div>

        <div class="input-grupo">
            <div class="input">
                <input type="submit" value="Gravar" />
            </div>
        </div>
    </form>
</div>