<?php
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

if ($tipo != 1 && $tipo != 5) {
    header('Location:/administrador/index.php');
    exit;
}

$id_rp = $_GET['id_rp'];

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
if ($id_rp) {
    $convites = $dbpagamentos->devolveConvites($id_rp);
    $extraSessao = $dbpagamentos->devolveExtras($id_rp, 1);
}
$extras = $dbpagamentos->devolveExtras($id_rp, 0);

$nome_extra  = $extras[0]['nome'];
$tipo_extra  = $extras[0]['tipo'];

?>

<h1 class="titulo"> Efecutar pagamento </h1>
<div class="paga">
    <div class="form">
        <div class="detalhe">
            <div class="titulo"> Informação de pagamento</div>
            <?php
            if (empty($id_rp)) { ?>

                <div class="bloco">
                    <div class="label">
                        Nome:
                    </div>
                    <div class="valor">
                        <input type="text" name="nome" value="<?php echo $nome_extra; ?>" />
                    </div>
                </div>
                <div class="bloco">
                    <div class="label">
                        Tipo de serviço:
                    </div>
                    <div class="valor">
                        <input type="text" name="tipo" value="<?php echo $tipo_extra; ?>" />
                    </div>
                </div>
            <?php
        } else {
            ?>
                <div class="bloco">
                    <div class="label">
                        Nome STAFF:
                    </div>
                    <div class="valor">
                        <?php echo $dbpagamentos->devolveNomeRP($id_rp); ?>
                    </div>
                </div>
            <?php
        }
        if ($tipo == 1) {
            ?>
                <div class="bloco">
                    <div class="label">
                        Desconta ao valor de caixa:
                    </div>
                    <div class="valor">
                        <?php echo $dbpagamentos->descontaValorCaixa() == 1?"Sim":"Não"; ?>
                    </div>
                </div>
            <?php
        }
        ?>
        </div>
        <?php
        if ($convites) {
            ?>
            <div class="convites">
                <div class="titulo"> Validar convite </div>
                <?php
                foreach ($convites as $convite) {
                    ?>
                    <div class="convite">
                        <div class="datas">
                            <div class="data">
                                <span class="label">
                                    Data de evento
                                </span>
                                <span class="valor">
                                    <?php echo $convite['data_evento']; ?>
                                </span>
                            </div>
                            <div class="data">
                                <span class="label">
                                    Data de upload
                                </span>
                                <span class="valor">
                                    <?php echo $convite['data']; ?>
                                </span>
                            </div>

                        </div>
                        <a href="<?php echo $convite['imagem']; ?>" href="image.jpg" data-fancybox data-caption="Convite para evento <?php echo $data_evento; ?>" class="imagem"> <img src="<?php echo $convite['imagem']; ?>">
                            <small> Clicar na foto para ver ampliada </small>
                        </a>
                        <div class="botoes">
                            <a href="javascript:;" data-valida="1" data-convite="<?php echo $convite['id']; ?>" class="botao confirmar <?php if ($convite['valido'] == 1) { ?> active <?php } ?>"> Confirmar </a>
                            <a href="javascript:;" data-valida="2" data-convite="<?php echo $convite['id']; ?>" class="botao recusar <?php if ($convite['valido'] == 2) { ?> active <?php } ?>"> Recusar </a>
                        </div>
                    </div>
                <?php
            }
            ?>
            </div>
        <?php
    }
    ?>
        <div class="extras">
            <div class="titulo"> Adicionar extras</div>
            <?php
            if ($dbpagamentos->devolveSessaoRP($id_rp) > 0) {
                ?>
                <div class="extra">
                    <div class="bloco">
                        <div class="label">
                            Descrição
                        </div>
                        <div class="input">
                            <input type="text" name="descricao" placeholder="Descrição do extra" value="Valor da sessão" readonly="readonly" />
                        </div>
                    </div>
                    <div class="bloco">
                        <div class="label">
                            Valor
                        </div>
                        <div class="input">
                            <input type="number" name="valor" step="0.01" readonly="readonly" value="<?php echo $dbpagamentos->devolveSessaoRP($id_rp); ?>" />
                            <input type="hidden" name="sessao" value="1" />
                            <input type="hidden" name="id" value="<?php echo $extraSessao[0]['id']; ?>" />
                        </div>
                    </div>
                    <div class="acao">
                        <a href="javascript:;" class="enviar <?php if ($extraSessao[0]['id'] > 0) { ?> active <?php } ?>"> Aplicar </a>
                        <a href="javascript:;" class="apagar"> Apagar </a>
                    </div>
                </div>
            <?php
        }
        if ($extras) {
            foreach ($extras as $extra) {
                include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/ajax/extras_pagamento.ajax.php";
            }
        }
        ?>
            <a href="#" class="novo_extra"> Adicionar novo extra </a>
        </div>
    </div>
    <div class="carrinho">
        <?php
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/ajax/carrinho.php";
        ?>
    </div>
</div>