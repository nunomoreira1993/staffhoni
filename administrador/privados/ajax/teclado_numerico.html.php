<?php 
$decimal = $_GET['decimal'];
?>

<div class="teclado-numerico">
    <div class="input">
        <input type="text" readonly="readonly" value="" name="numero" />
    </div>
    <div class="calculadora">
        <?php
        for ($i = 1; $i <= 9; $i++) {
            ?>
            <a href="#" data-numero="<?php echo $i; ?>" class="numero">
                <?php echo $i; ?>
            </a>
        <?php
        }
        ?>
        <a href="#" data-numero="0" class="numero">
            0
        </a>
        <?php if ($decimal == 1) {  ?>
            <a href="#" data-numero="." class="numero">
                ,
            </a>
        <?php
        }
        ?>
        <a href="#" data-numero="delete" class="apagar">
                🠐
            </a>
        <a href="#" data-numero="ok" class="ok">
            Confirmar
        </a>
    </div>
</div>