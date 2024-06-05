<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/privados/privados.obj.php');
$dbprivados = new privados($db);
$venda = $dbprivados->devolveVendaGarrafas($_GET['id']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Honi - Gestão de RP's &amp; Promotores</title>
    <link href="/temas/administrador/css/imprimir.css?v=1" rel="stylesheet" media="all" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
</head>

<body>
    <div class="topo">
        <span class="logotipo"><img src="/temas/administrador/imagens/logotipo.png" /> </span>
        <h1 class="venda"> VENDA DE GARRAFA </h1>
    </div>
    <div class="cabecalho">
        <div class="esquerda">
            <div class="bloco">
                <span class="label">
                    Número:
                </span>
                <span class="valor">
                    <?php echo $venda['id']; ?>
                </span>
            </div>
            <div class="bloco">
                <span class="label">
                    Data da compra
                </span>
                <span class="valor">
                    <?php echo $venda['data']; ?>
                </span>
            </div>
            <div class="bloco">
                <span class="label">
                    Data do evento
                </span>
                <span class="valor">
                    <?php echo $venda['data_evento']; ?>
                </span>
            </div>
            <div class="bloco">
                <span class="label">
                    Nº Cartões
                </span>
                <span class="valor">
                    <?php echo $venda['numero_cartoes']; ?>
                </span>
            </div>
        </div>
        <div class="direita">
            <?php
            if ($venda['nome_processado']) {
                ?>
                <div class="bloco">
                    <span class="label">
                        Factura processada por:
                    </span>
                    <span class="valor">
                        <?php echo $venda['nome_processado']; ?>
                    </span>
                </div>
            <?php
        }
        if ($venda['nome_bar']) {
            ?>
                <div class="bloco">
                    <span class="label">
                        Bar:
                    </span>
                    <span class="valor">
                        <?php echo $venda['nome_bar']; ?>
                    </span>
                </div>
            <?php
        }
        ?>
            <div class="bloco">
                <span class="label">
                    Nome do Cliente:
                </span>
                <span class="valor">
                    <?php echo $venda['nome_cliente']; ?>
                </span>
            </div>
            <div class="bloco">
                <span class="label">
                    Angariador:
                </span>
                <span class="valor">
                    <?php echo $venda['nome_rp']; ?>
                </span>
            </div>
        </div>
    </div>
    <table class="garrafas" cellpadding="0" cellspacing="0">
        <tr>
            <th>
                Garrafa
            </th>
            <th>
                Quantidade
            </th>
        </tr>
        <?php
        foreach ($venda['garrafas'] as $garrafa) {
            ?>
            <tr>
                <td>
                    <?php echo $garrafa['nome']; ?>
                </td>
                <td>
                    <?php echo $garrafa['quantidade']; ?>
                </td>
            </tr>
        <?php
    }
    ?>
    </table>
    <?php
    if ($venda['valor_multibanco']) {
        ?>
        <div class="pagamento">
            <div class="layer">
                Valor pago em multibanco
            </div>
            <div class="valor">
                <?php echo euro($venda['valor_multibanco']); ?>
            </div>
        </div>
    <?php
}
if ($venda['valor_dinheiro']) {
    ?>
        <div class="pagamento">
            <div class="layer">
                Valor pago em dinheiro
            </div>
            <div class="valor">
                <?php echo euro($venda['valor_dinheiro']); ?>
            </div>
        </div>
    <?php
}
?>
    <div class="total">
        <div class="layer">
            Total
        </div>
        <div class="valor">
            <?php echo euro($venda['total']); ?>
        </div>
    </div>

</body>
<script language="Javascript1.2">
    //<!--
    window.print();
    //-->
</script>

</html>