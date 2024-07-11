<?php
if (isset($_GET['pg'])) {
    $pg = $_GET['pg'];
} else {
    $pg = "";
}

if ($tipo == 1) {
    $default =  $_SERVER['DOCUMENT_ROOT'] . "/administrador/administradores/administradores.php";
} else if ($tipo == 2) {
    $default =  $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/qrcode.php";
} else if ($tipo == 3) {
    $default = $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/venda_privados.php";
} else if ($tipo == 4) {
    $default = $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/venda_privados.php";
} else if ($tipo == 5) {
    $default = $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/pagamentos.php";
}else if ($tipo == 6) {
    $default = $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/presencas.php";
} else if ($tipo == 7) {
    $default = $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/entrada_privados.php";
}
switch ($pg) {

    case 'administradores':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/administradores/administradores.php";
        break;

    case 'rps':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/rps/rps.php";
        break;

    case 'inserir_administrador':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/administradores/inserir_administrador.php";
        break;

    case 'inserir_rp':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/rps/inserir_rp.php";
        break;

    case 'adicionar_entradas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/adicionar_entradas.php";
        break;

    case 'gerir_entradas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/gerir_entradas.php";
        break;

    case 'entradas_rp':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/rps/entradas_rp.php";
        break;

    case 'qrcode':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/qrcode.php";
        break;

    case 'eventos_entradas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/eventos_entradas.php";
        break;

    case 'entradas_evento_rps':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/entradas_evento_rps.php";
        break;

    case 'entradas_evento_produtor':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/entradas_evento_produtor.php";
        break;

    case 'entradas_evento_equipa':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/entradas_evento_equipa.php";
        break;

    case 'entradas_evento_rps':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/entradas_evento_rps.php";
        break;

    case 'entradas_evento_data':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/entradas_evento_data.php";
        break;

    case 'cartoes_consumo_obrigatorio':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/cartoes_consumo_obrigatorio.php";
        break;

    case 'cartoes_sem_consumo':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/entradas/cartoes_sem_consumo.php";
        break;

    case 'gestao_salas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/gestao_salas.php";
        break;

    case 'gestao_mesas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/gestao_mesas.php";
        break;

    case 'inserir_sala':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/inserir_sala.php";
        break;

    case 'inserir_mesa':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/inserir_mesa.php";
        break;

    case 'gestao_garrafas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/gestao_garrafas.php";
        break;

    case 'inserir_garrafa':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/inserir_garrafa.php";
        break;

    case 'gestao_bares':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/gestao_bares.php";
        break;

    case 'inserir_bares':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/inserir_bares.php";
        break;

    case 'venda_privados':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/venda_privados.php";
        break;

    case 'venda_garrafas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/venda_garrafas.php";
        break;

    case 'historico_privados':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/historico_privados.php";
        break;

    case 'historico_privados':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/historico_privados.php";
        break;

    case 'privados_evento_data':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/privados_evento_data.php";
        break;

    case 'historico_garrafas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/historico_garrafas.php";
        break;

    case 'garrafas_evento_data':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/garrafas_evento_data.php";
        break;

    case 'inserir_venda_privados':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/inserir_venda_privados.php";
        break;

    case 'inserir_venda_garrafas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/inserir_venda_garrafas.php";
        break;

    case 'escolher_mesa_privados':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/escolher_mesa_privados.php";
        break;

    case 'reservas_garrafas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/reservas_garrafas.php";
        break;

    case 'logica_atrasos':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/logica_atrasos.php";
        break;

    case 'logica_pagamentos':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/logica_pagamentos.php";
        break;

    case 'inserir_logica_atrasos':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/inserir_logica_atrasos.php";
        break;

    case 'inserir_logica_pagamentos':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/inserir_logica_pagamentos.php";
        break;

    case 'presencas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/presencas.php";
        break;

    case 'pagamentos':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/pagamentos.php";
        break;

    case 'insere_pagamentos':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/insere_pagamentos.php";
        break;

    case 'insere_caixa':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/insere_caixa.php";
        break;

    case 'relatorio_pagamentos':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/relatorio_pagamentos.php";
        break;

    case 'ver_pagamentos':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/ver_pagamentos.php";
        break;

    case 'relatorio_entradas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/relatorio_entradas.php";
        break;

    case 'ver_entradas':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/ver_entradas.php";
        break;

    case 'valores_caixa':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/valores_caixa.php";
        break;

    case 'ver_pagamentos_detalhe':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/ver_pagamentos_detalhe.php";
        break;

    case 'insere_cartao':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/insere_cartao.php";
        break;

    case 'convites':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/convites.php";
        break;

    case 'ver_convites':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/ver_convites.php";
        break;

    case 'editar_convite':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/pagamentos/editar_convite.php";
        break;

    case 'entrada_privados':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/privados/entrada_privados.php";
        break;

    case 'estatisticas_privados':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/estatisticas/estatisticas_privados.php";
        break;

    case 'estatisticas_rp':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/estatisticas/estatisticas_rp.php";
        break;

    case 'estatisticas_chefe':
        include $_SERVER['DOCUMENT_ROOT'] . "/administrador/estatisticas/estatisticas_chefe.php";
        break;

    default:
        include  $default;
        break;
}
