<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
?>
<div class="extra">
    <div class="bloco">
        <div class="label">
            Descrição
        </div>
        <div class="input">
            <input type="text" name="descricao" value="<?php echo $extra['descricao']; ?>" placeholder="Descrição do extra" />
        </div>
    </div>
    <div class="bloco">
        <div class="label">
            Valor
        </div>
        <div class="input">
            <input type="number" name="valor" step="0.01" value="<?php echo $extra['valor']; ?>" />
            <input type="hidden" name="sessao" value="<?php echo intval($extra['sessao']); ?>" />
            <input type="hidden" name="id" value="<?php echo $extra['id']; ?>" />
        </div>
    </div>
    <div class="acao">
        <a href="javascript:;" class="enviar  <?php if ($extra['id'] > 0) { ?> active <?php } ?>"> Aplicar </a>
        <a href="javascript:;" class="apagar"> Apagar </a>
    </div>
</div>