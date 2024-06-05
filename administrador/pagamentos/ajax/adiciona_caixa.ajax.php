<?php
if (isset($_GET['index'])) {
    $k = $_GET['index'];
} else if (empty($k)) {
    $k = 0;
}
if (empty($vcaixa)) {
    $vcaixa['numero'] = "";
    $vcaixa['valor'] = "";
}
?>
<div class="caixa">
    <div class="bloco">
        <div class="label">
            Número da caixa:
        </div>
        <div class="input">
            <input type="number" name="caixa[<?php echo $k; ?>][numero]" placeholder="Número da caixa" value="<?php echo $vcaixa['numero']; ?>" />
        </div>
    </div>
    <div class="bloco">
        <div class="label">
            Valor (€)
        </div>
        <div class="input">
            <input type="number" name="caixa[<?php echo $k; ?>][valor]" step="0.01" value="<?php echo $vcaixa['valor']; ?>" />
        </div>
    </div>
</div>