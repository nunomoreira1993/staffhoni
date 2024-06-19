<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
ob_start();
if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/administradores/administrador.obj.php');
$dbadministrador = new administrador($db);
$adm = $dbadministrador->devolveUtilizador($_SESSION['id_utilizador']);
$tipo = $adm['tipo'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Honi - Gestão de RP's &amp; Promotores</title>
    <link href="/temas/administrador/css/style.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/css/style.css"); ?>" rel="stylesheet" media="all" />
    <link href="/temas/administrador/css/style-1090.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/css/style-1090.css"); ?>" rel="stylesheet" media="all" />
    <link href="/temas/administrador/css/style-768.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/css/style-768.css"); ?>" rel="stylesheet" media="all" />

    <link href="/temas/administrador/css/swiper.min.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/css/swiper.min.css"); ?>" rel="stylesheet" media="all" async />
    <link href="/temas/administrador/css/teclado.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/css/teclado.css"); ?>" rel="stylesheet" media="all" />
    <link href="/temas/administrador/css/fancybox.css" rel="stylesheet" media="all" async />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
</head>

<body>
    <div class="header">
        <a href="/administrador/" class="logotipo"> <img src="/temas/login/imagens/logotipo.png" /> </a>
        <a class="hamburger" href="#"><span></span><span></span><span></span><span></span></a>
        <div class="utilizador">
            <div class="botao">
                <span class="imagem">
                    <?php
                    if (!empty($adm['foto']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/administradores/" . $adm['foto'])) {
                    ?>
                        <img src="/fotos/administradores/<?php echo $adm['foto']; ?>">
                    <?php
                    }
                    ?>
                </span>
                <span class="nome"> <?php echo $adm['nome']; ?></span>
            </div>
            <div class="sub-menu">
                <?php
                if ($_SESSION['id_processado']) {
                ?>
                    <a href="/rp/" class="dropdown-item"><i class="ti-user"></i> Voltar para conta Staff</a>
                <?php
                } else {
                ?>
                    <!-- text-->
                    <a href="/administrador/index.php?pg=inserir_administrador&id=<?php echo $adm['id'] ?>" class="dropdown-item"><i class="ti-user"></i> O meu perfil</a>
                <?php
                }
                ?>
                <!-- text-->
                <div class="divisoria"></div>
                <!-- text-->
                <a href="/administrador/logout.php" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                <!-- text-->
            </div>
        </div>
    </div>
    <div class="menu-lateral">
        <div class="scroll">
            <ul>
                <?php
                if ($tipo == 1) {
                ?>
                    <li <?php if ($_GET['pg'] == "administradores") { ?> class="active" <?php } ?>>
                        <a class="link" href="?pg=administradores" aria-expanded="false">
                            <i class="icone"></i>
                            <span class="nome">Administradores</span>
                        </a>
                    </li>
                    <li <?php if ($_GET['pg'] == "rps") { ?> class="active" <?php } ?>>
                        <a class="link" href="?pg=rps" aria-expanded="false">
                            <i class="icone"></i>
                            <span class="nome">Staff</span>
                        </a>
                    </li>
                <?php
                }
                if ($tipo == 2 || $tipo == 1 || $tipo == 7) {
                ?>
                    <li <?php if ($_GET['pg'] == "adicionar_entradas" || $_GET['pg'] == "gerir_entradas" || $_GET['pg'] == "qrcode" || $_GET['pg'] == "eventos_entradas" || $_GET['pg'] == "entradas_evento_rps" || $_GET['pg'] == "entradas_evento_produtor" || $_GET['pg'] == "entradas_evento_equipa" || $_GET['pg'] == "cartoes_consumo_obrigatorio" || $_GET['pg'] == "cartoes_sem_consumo" || $_GET['pg'] == "entradas_evento_data") { ?> class="active" <?php } ?>>
                        <a class="link" href="javascript:void(0)" aria-expanded="false">
                            <i class="icone"></i>
                            <span class="nome">Entradas</span>
                        </a>
                        <ul aria-expanded="false" class="collapse in">
                            <?php
                            if($tipo == 7){
                                ?>
                                <li>
                                    <a href="?pg=cartoes_sem_consumo" <?php if ($_GET['pg'] == "cartoes_sem_consumo") { ?> class="active" <?php } ?>>
                                        Cartões sem consumo
                                    </a>
                                </li>
                                <?php
                            }
                            else {
                                ?>
                                <li>
                                    <a href="?pg=qrcode" <?php if ($_GET['pg'] == "qrcode") { ?> class="active" <?php } ?>>
                                        Leitor QR Code
                                    </a>
                                </li>
                                <li>
                                    <a href="?pg=adicionar_entradas&letra=a" <?php if ($_GET['pg'] == "adicionar_entradas") { ?> class="active" <?php } ?>>
                                        Adicionar entradas
                                    </a>
                                </li>
                                <li>
                                    <a href="?pg=gerir_entradas" <?php if ($_GET['pg'] == "gerir_entradas") { ?> class="active" <?php } ?>>
                                        Gerir entradas
                                    </a>
                                </li>
                                <li>
                                    <a href="?pg=cartoes_consumo_obrigatorio" <?php if ($_GET['pg'] == "cartoes_consumo_obrigatorio") { ?> class="active" <?php } ?>>
                                        Embaixadores
                                    </a>
                                </li>
                                <li>
                                    <a href="?pg=cartoes_sem_consumo" <?php if ($_GET['pg'] == "cartoes_sem_consumo") { ?> class="active" <?php } ?>>
                                        Cartões sem consumo
                                    </a>
                                </li>
                                <li>
                                    <a href="?pg=eventos_entradas" <?php if ($_GET['pg'] == "eventos_entradas" || $_GET['pg'] == "entradas_evento_rps" || $_GET['pg'] == "entradas_evento_data"  || $_GET['pg'] == "entradas_evento_produtor" || $_GET['pg'] == "entradas_evento_equipa") { ?> class="active" <?php } ?>>
                                        Eventos
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </li>
                <?php

                }
                if ($tipo == 3  || $tipo == 1 || $tipo == 4 || $tipo == 7) {

                ?>
                    <li <?php if ($_GET['pg'] == "gestao_salas" || $_GET['pg'] == "gestao_bares" || $_GET['pg'] == "inserir_bares" || $_GET['pg'] == "inserir_sala" ||  $_GET['pg'] == "inserir_garrafa" || $_GET['pg'] == "escolher_mesa_privados" || $_GET['pg'] == "inserir_venda_privados" || $_GET['pg'] == "inserir_venda_garrafas" || $_GET['pg'] == "gestao_mesas" || $_GET['pg'] == "venda_privados" || $_GET['pg'] == "venda_garrafas" || $_GET['pg'] == "gestao_garrafas" || $_GET['pg'] == "historico_privados" || $_GET['pg'] == "historico_garrafas" || $_GET['pg'] == "privados_evento_data" || $_GET['pg'] == "garrafas_evento_data" || $_GET['pg'] == "reservas_garrafas" || $_GET['pg'] == "entrada_privados") { ?> class="active" <?php } ?>>
                        <a class="link" href="javascript:void(0)" aria-expanded="false">
                            <i class="icone"></i>
                            <span class="nome">Privados</span>
                        </a>
                        <ul aria-expanded="false" class="collapse in">
                            <li>
                                <a href="?pg=entrada_privados" <?php if ($_GET['pg'] == "entrada_privados" || ($tipo == 7 && empty($_GET['pg']))) { ?> class="active" <?php } ?>>
                                    Entrada de privados
                                </a>
                                <?php
                                if ($tipo == 1) {
                                ?>
                                    <a href="?pg=gestao_salas" <?php if ($_GET['pg'] == "gestao_salas" || $_GET['pg'] == "inserir_sala" || $_GET['pg'] == "gestao_mesas") { ?> class="active" <?php } ?>>
                                        Gerir Salas
                                    </a>
                                    <a href="?pg=gestao_garrafas" <?php if ($_GET['pg'] == "gestao_garrafas" || $_GET['pg'] == "inserir_garrafa") { ?> class="active" <?php } ?>>
                                        Gestão de Garrafas
                                    </a>
                                    <a href="?pg=gestao_bares" <?php if ($_GET['pg'] == "gestao_bares" || $_GET['pg'] == "inserir_bares") { ?> class="active" <?php } ?>>
                                        Gestão de Bares
                                    </a>
                                <?php
                                }
                                if ($tipo != 7) {
                                ?>
                                    <a href="?pg=venda_privados" <?php if ($_GET['pg'] == "venda_privados" || $_GET['pg'] == "escolher_mesa_privados" || $_GET['pg'] == "inserir_venda_privados") { ?> class="active" <?php } ?>>
                                        Venda de Privados
                                    </a>
                                    <a href="?pg=venda_garrafas" <?php if ($_GET['pg'] == "venda_garrafas" || $_GET['pg'] == "inserir_venda_garrafas") { ?> class="active" <?php } ?>>
                                        Venda de Garrafas
                                    </a>
                                    <a href="?pg=historico_privados" <?php if ($_GET['pg'] == "historico_privados" || $_GET['pg'] == "privados_evento_data") { ?> class="active" <?php } ?>>
                                        Histórico Privados
                                    </a>
                                    <a href="?pg=historico_garrafas" <?php if ($_GET['pg'] == "historico_garrafas" || $_GET['pg'] == "garrafas_evento_data") { ?> class="active" <?php } ?>>
                                        Histórico Garrafas
                                    </a>
                                    <a href="?pg=reservas_garrafas" <?php if ($_GET['pg'] == "reservas_garrafas") { ?> class="active" <?php } ?>>
                                        Reservas de Garrafas
                                    </a>
                                <?php
                                }
                                ?>
                            </li>
                        </ul>
                    </li>
                <?php
                }
                if ($tipo == 1 || $tipo == 5 || $tipo == 4 || $tipo == 6 || $tipo == 2) {
                ?>
                    <li <?php if ($_GET['pg'] == "logica_pagamentos" || $_GET['pg'] == "insere_pagamentos"  || $_GET['pg'] == "inserir_logica_pagamentos" || $_GET['pg'] == "logica_atrasos" || $_GET['pg'] == "inserir_logica_atrasos" ||  $_GET['pg'] == "convites" || $_GET['pg'] == "presencas" || $_GET['pg'] == "pagamentos" || $_GET['pg'] == "inserir_pagamento"  || $_GET['pg'] == "relatorio_pagamentos" || $_GET['pg'] == "valores_caixa" || $_GET['pg'] == "ver_pagamentos" || $_GET['pg'] == "insere_caixa" || $_GET['pg'] == "ver_entradas" || $_GET['pg'] == "relatorio_entradas" || $_GET['pg'] == "ver_pagamentos_detalhe" || $_GET['pg'] == "ver_convites" || $_GET['pg'] == "editar_convite" || $_GET['pg'] == "insere_cartao") { ?> class="active" <?php } ?>>
                        <a class="link" href="javascript:void(0)" aria-expanded="false">
                            <i class="icone"></i>
                            <span class="nome">Pagamentos</span>
                        </a>
                        <ul aria-expanded="false" class="collapse in">
                            <li>
                                <?php
                                if ($tipo == 1) {
                                ?>
                                    <a href="?pg=logica_pagamentos" <?php if ($_GET['pg'] == "logica_pagamentos" || $_GET['pg'] == "inserir_logica_pagamentos") { ?> class="active" <?php } ?>>
                                        Lógica Pagamentos Guest
                                    </a>
                                    <a href="?pg=logica_atrasos" <?php if ($_GET['pg'] == "logica_atrasos" || $_GET['pg'] == "inserir_logica_atrasos") { ?> class="active" <?php } ?>>
                                        Lógica Atrasos
                                    </a>
                                <?php
                                }
                                if ($tipo == 4 || $tipo == 6 || $tipo == 1 || $tipo == 2) {
                                ?>
                                    <a href="?pg=presencas" <?php if ($_GET['pg'] == "presencas") { ?> class="active" <?php } ?>>
                                        Entradas Staff
                                    </a>
                                    <a href="?pg=relatorio_entradas" <?php if ($_GET['pg'] == "relatorio_entradas" || $_GET['pg'] == "ver_entradas") { ?> class="active" <?php } ?>>
                                        Relatório de Entradas Staff
                                    </a>
                                <?php
                                }

                                if ($tipo == 5 || $tipo == 1) {
                                ?>
                                    <a href="?pg=convites" <?php if ($_GET['pg'] == "convites" || $_GET['pg'] == "ver_convites" || $_GET['pg'] == "editar_convite") { ?> class="active" <?php } ?>>
                                        Convites
                                    </a>
                                    <a href="?pg=pagamentos&letra=a" <?php if ($_GET['pg'] == "pagamentos" || $_GET['pg'] == "insere_pagamentos" || $_GET['pg'] == "inserir_pagamento" || $_GET['pg'] == "insere_caixa") { ?> class="active" <?php } ?>>
                                        Pagamentos
                                    </a>
                                    <a href="?pg=relatorio_pagamentos" <?php if ($_GET['pg'] == "relatorio_pagamentos" || $_GET['pg'] == "valores_caixa" || $_GET['pg'] == "ver_pagamentos"  || $_GET['pg'] == "ver_pagamentos_detalhe") { ?> class="active" <?php } ?>>
                                        Relatório de pagamentos
                                    </a>
                                <?php
                                }
                                ?>
                            </li>
                        </ul>
                    </li>
                <?php
                }
                ?>

            </ul>
        </div>
    </div>
    <div class="conteudo">
        <?php
        include "paginas.php";
        ?>
    </div>
    <?php
    if ($tipo == 3 && empty($_SESSION['id_processado'])) {
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/pin.php";
    }
    ?>
    <script src="/temas/administrador/js/jquery.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/js/jquery.js"); ?>"></script>
    <script src="/temas/administrador/js/script.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/js/script.js"); ?>"></script>
    <script src="/temas/administrador/js/sweetalert.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/js/sweetalert.js"); ?>"></script>
    <script src="/temas/administrador/js/swiper.min.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/js/swiper.min.js"); ?>"></script>
    <script src="/temas/administrador/js/fancybox.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/js/fancybox.js"); ?>"></script>
    <script src="/temas/administrador/js/teclado.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/js/teclado.js"); ?>"></script>
</body>

</html>

<?php
ob_end_flush();
?>