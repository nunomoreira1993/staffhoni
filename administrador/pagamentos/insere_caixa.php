<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
if ($tipo != 1 && $tipo != 5) {
    header('Location:/administrador/index.php');
    exit;
}

if (date('H') < 14) {
    $data = date('Y-m-d', strtotime('-1 day'));
}
else {
    $data = date('Y-m-d');
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
$valores_caixa = $dbpagamentos->devolveValoresCaixas($data);
if ($_POST) {

    if ($valores_caixa) {
        $dbpagamentos->apagaValoresCaixas($data);
    }
    foreach ($_POST['caixa'] as $caixa) {
        if ($caixa['numero'] && $caixa['valor']) {
            $campos['numero'] = $caixa['numero'];
            $campos['valor'] = $caixa['valor'];
            $campos['data'] = $data;
            $db->Insert('conta_corrente_caixas', $campos);
        }
    }

    $_SESSION['sucesso'] = "Valores em caixa registados com sucesso.";
    header('Location: /administrador/index.php?pg=pagamentos');
    exit;
}
?>

<h1 class="titulo"> Dinheiro em caixa para pagamentos em <?php echo $data; ?></h1>
<form name="" action="" method="POST" class="caixas">
    <?php
    $k = 0;
    if ($valores_caixa) {
        foreach ($valores_caixa as $k => $vcaixa) {
            include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/ajax/adiciona_caixa.ajax.php";
        }
    } else {
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/ajax/adiciona_caixa.ajax.php";
    }
    ?>
    <a href="#" class="nova_caixa" data-index="<?php echo $k; ?>"> Adicionar nova caixa </a>
    <input type="submit" value="Inserir valores de caixa" />
</form>