<?php
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
if (!empty($_SESSION['id_utilizador'])) {
    header('Location:/administrador/index.php');
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Honi - Gestão de RP's &amp; Promotores</title>
    <link href="/temas/login/css/style.css?v=1548235201" rel="stylesheet" media="all" />
    <link href="/temas/login/css/style-1090.css?v=1548235274" rel="stylesheet" media="all" />
    <link href="/temas/login/css/style-768.css?v=1548235201" rel="stylesheet" media="all" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <link rel="apple-touch-icon" sizes="180x180" href="/temas/login/imagens/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/temas/login/imagens/favicons/favicon-32x32.png">
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
    <?php
    if ($_POST) {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/administradores/administrador.obj.php');
        $password = $_POST['password'];
        $email = $_POST['email'];
        $dbadministrador = new administrador($db);
        $erro = $dbadministrador->verificaAdministrador($email, $password);
        if (empty($erro)) {
            $res = $dbadministrador->setSession($email, $password);
            header('Location: /administrador/index.php');
            exit;
        }
        if ($erro) {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/rps/rps.obj.php');
            $dbrps = new rps($db);
            $erro = $dbrps->verificaRP($email, $password);
            if (empty($erro)) {
                $res = $dbrps->setSession($email, $password);
                header('Location: /rp/index.php');
                exit;
            }
        }
    }
    ?>
    <div class="apresentacao">
        <div class="conteudo">
            <h1> GESTÃO DE RP'S &amp; PROMOTORES </h1>
            <h2> Bem-vindo </h2>
            <div class="descricao">
                Digita as tuas credenciais para entrares.
            </div>
        </div>
    </div>
    <form id="login" action="/" method="post" data-erro="<?php echo $erro; ?>">
        <div class="form-login">
            <div class="logotipo">
                <img src="/temas/login/imagens/logotipo-entrada.png" />
            </div>
            <div class="formCampos">
                <div class="input">
                    <div class="label">
                        E-mail / Nº Telemóvel
                    </div>
                    <div class="inputs">
                        <input id="email" name="email" type="text" placeholder="Insira o seu e-mail ou numero de telemóvel" required="">
                    </div>
                </div>
                <div class="input">
                    <div class="label">
                        Password
                    </div>
                    <div class="inputs">
                        <input id="password" name="password" type="password" placeholder="Insira a sua password" required="">
                    </div>
                </div>
                <!-- <a href="?pg=recuperar" class="recuperar">
                                Esqueceu-se da password?
                            </a> -->
            </div>
            <div class="submit">
                <input type="submit" id="submit" class="add-button" value="Login">
            </div>
        </div>
    </form>

    <script src="/temas/administrador/js/jquery.js"></script>
    <script src="/temas/login/js/script.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . "/temas/login/js/script.js"); ?>"></script>
    <script src="/temas/administrador/js/sweetalert.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js', {
                    scope: '/'
                })
                .then(function(registration) {
                    console.log('Service Worker Registered');
                });

            navigator.serviceWorker.ready.then(function(registration) {
                console.log('Service Worker Ready');
            });
        }
    </script>
</body>

</html>
<?php
ob_end_flush();
?>