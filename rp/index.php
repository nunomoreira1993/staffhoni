<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
ob_start();
if (empty($_SESSION['id_rp'])) {
    header('Location:/index.php');
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
$dbrp = new rp($db, $_SESSION['id_rp']);
$altera = $dbrp->alterouPassword();
if ($altera == 0 && strpos($_SERVER['REQUEST_URI'], 'alterar_password') === false) {
    header('Location: /rp/index.php?pg=alterar_password');
    exit;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Honi - Gestão de RP's &amp; Promotores</title>
    <link href="/temas/rps/css/style.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/rps/css/style.css"); ?>" rel="stylesheet" media="all" />

    <link href="/temas/administrador/css/swiper.min.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/administrador/css/swiper.min.css"); ?>" rel="stylesheet" media="all" async />
    <link href="/temas/administrador/css/fancybox.css" rel="stylesheet" media="all" async />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <link rel="apple-touch-icon" sizes="180x180" href="/temas/login/imagens/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="194x194" href="/temas/login/imagens/favicons/favicon-194x194.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/temas/login/imagens/favicons/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/temas/login/imagens/favicons/favicon-16x16.png">
    <link rel="manifest" href="/temas/login/imagens/favicons/site.webmanifest">
    <link rel="mask-icon" href="/temas/login/imagens/favicons/safari-pinned-tab.svg" color="#000000">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/temas/login/imagens/favicons/mstile-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>

<body>
    <div class="menu">
        <div class="scroll">
            <?php
            include $_SERVER['DOCUMENT_ROOT'] . "/rp/menu.php";
            ?>
        </div>
    </div>

    <div class="content" <?php echo escreveErroSucesso(); ?>>
        <?php
        if ($altera != 0) {
            ?>
            <a href="#" class="hambburger">
                <span></span><span></span><span></span><span></span>
            </a>
        <?php
    }
    ?>
        <?php
        if (empty($_GET['pg']) || $altera == 0) {
            ?>
            <a href="/rp/logout.php" class="logout">
                <img src="/temas/rps/imagens/logout.svg" />
            </a>
        <?php

    } else {
        ?>
            <a href="/rp/index.php" class="logout">
                <img src="/temas/rps/imagens/homepage_laranja.svg" />
            </a>

        <?php
    }
    include $_SERVER['DOCUMENT_ROOT'] . "/rp/paginas.php";
    ?>
    </div>
    <script src="/temas/administrador/js/jquery.js"></script>
    <script src="/temas/rps/js/script.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/rps/js/script.js"); ?>"></script>
    <script src="/temas/administrador/js/sweetalert.js"></script>
    <script src="/temas/administrador/js/swiper.min.js"></script>
    <script src="/temas/administrador/js/fancybox.js"></script>
</body>

</html>

<?php
ob_end_flush();
?>