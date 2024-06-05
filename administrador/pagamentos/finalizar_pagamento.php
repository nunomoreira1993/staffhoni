<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";

if (empty($_SESSION['id_utilizador'])) {
    header('Location:/index.php');
    exit;
}

$id_rp = intval($_GET['id_rp']);

require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
$pagamento = $dbpagamentos->devolvePagamento($id_rp);

$datas_pagamento = $dbpagamentos->devolveDatasParaPagamento($id_rp);

if (date('H') < 14) {
    $data_evento = date('Y-m-d', strtotime('-1 day'));
} else {
    $data_evento = date('Y-m-d');
}

if ($dbpagamentos->devolveDiferencaTotalCaixa($data_evento) < $pagamento['total'] && $dbpagamentos->descontaValorCaixa() == 1) {
    $_SESSION['erro'] = "O total de pagamento é superior ao valor de caixa. Por favor adicione mais fundos na caixa para efectuar o pagamento.";
    header('Location: /administrador/index.php?pg=pagamentos');
    exit;
}

if (!empty($datas_pagamento) || $id_rp == 0) {

    $campo_geral['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $campo_geral['ip'] = $_SERVER['REMOTE_ADDR'];
    $campo_geral['total'] = $pagamento['total'];
    $campo_geral['data'] = date('Y-m-d H:i:s');
    $campo_geral['data_evento'] = $data_evento;
    $campo_geral['id_rp'] = $id_rp;
    $campo_geral['id_administrador'] = $_SESSION['id_utilizador'];
    $campo_geral['nome'] = $pagamento['extras']['items'][0]['nome'];
    $campo_geral['tipo'] = $pagamento['extras']['items'][0]['tipo'];
    $campo_geral['pagamento_caixa'] = $dbpagamentos->descontaValorCaixa();

    $id_conta_corrente = $db->Insert('conta_corrente', $campo_geral);

    foreach ($datas_pagamento as $data) {
        $campo_eventos['id_rp'] = $id_rp;
        $campo_eventos['id_conta_corrente'] = $id_conta_corrente;
        $campo_eventos['data_evento'] = $data['data_evento'];
        $db->Insert('conta_corrente_eventos', $campo_eventos);
    }

    if ($id_conta_corrente) {
        if ($id_rp) {
            if ($pagamento['guest']) {
                unset($campo);
                $campo['id_conta_corrente'] = $id_conta_corrente;
                $campo["valor"] = $pagamento["guest"]["comissao"];
                $campo["descricao"] = $pagamento["guest"]["descricao"];
                $campo["nome"] = "Comissão Guest";
                $id_conta_corrente_linha = $db->Insert('conta_corrente_linhas', $campo);

                if($pagamento["guest"]["comissao_bonus"] > 0){
                    unset($campo);
                    $campo['id_conta_corrente'] = $id_conta_corrente;
                    $campo["valor"] = $pagamento["guest"]["comissao_bonus"];
                    $campo["descricao"] = $pagamento["guest"]["descricao_bonus"];
                    $campo["nome"] = "Comissão Guest";
                    $id_conta_corrente_linha = $db->Insert('conta_corrente_linhas', $campo);
                }
            }
            if ($pagamento['guest_team']) {
                unset($campo);
                $campo['id_conta_corrente'] = $id_conta_corrente;
                $campo["valor"] = $pagamento["guest_team"]["comissao"];
                $campo["descricao"] = $pagamento["guest_team"]["descricao"];
                $campo["nome"] = "Comissão Guest - Equipa";
                $id_conta_corrente_linha = $db->Insert('conta_corrente_linhas', $campo);

                if($pagamento["guest_team"]["comissao_bonus"] > 0){
                    unset($campo);
                    $campo['id_conta_corrente'] = $id_conta_corrente;
                    $campo["valor"] = $pagamento["guest_team"]["comissao_bonus"];
                    $campo["descricao"] = $pagamento["guest_team"]["descricao_bonus"];
                    $campo["nome"] = "Comissão Guest - Equipa";
                    $id_conta_corrente_linha = $db->Insert('conta_corrente_linhas', $campo);
                }
            }
            if ($pagamento['privados']) {
                unset($campo);
                $campo['id_conta_corrente'] = $id_conta_corrente;
                $campo["valor"] = $pagamento["privados"]["comissao"];
                $campo["descricao"] = $pagamento["privados"]["descricao"];
                $campo["nome"] = "Privados";
                $id_conta_corrente_linha = $db->Insert('conta_corrente_linhas', $campo);
            }
            if ($pagamento['privados_chefe']) {
                unset($campo);
                $campo['id_conta_corrente'] = $id_conta_corrente;
                $campo["valor"] = $pagamento["privados_chefe"]["comissao"];
                $campo["descricao"] = $pagamento["privados_chefe"]["descricao"];
                $campo["nome"] = "Privados Equipa";
                $id_conta_corrente_linha = $db->Insert('conta_corrente_linhas', $campo);
            }
            if ($pagamento['garrafas']) {
                unset($campo);
                $campo['id_conta_corrente'] = $id_conta_corrente;
                $campo["valor"] = $pagamento["garrafas"]["comissao"];
                $campo["descricao"] = $pagamento["garrafas"]["descricao"];
                $campo["nome"] = "Garrafas Bar";
                $id_conta_corrente_linha = $db->Insert('conta_corrente_linhas', $campo);
                $paga = $db->Update('venda_garrafas_bar', array('paga' => 1), 'id_rp = ' . $id_rp);
            }
            if ($pagamento['atrasos']) {
                unset($campo);
                $campo['id_conta_corrente'] = $id_conta_corrente;
                $campo["valor"] = -$pagamento["atrasos"]["comissao"];
                $campo["descricao"] = $pagamento["atrasos"]["descricao"];
                $campo["nome"] = "Atraso";
                $id_conta_corrente_linha = $db->Insert('conta_corrente_linhas', $campo);
            }
            if ($pagamento['convites']) {
                unset($campo);
                $campo['id_conta_corrente'] = $id_conta_corrente;
                $campo["valor"] = -$pagamento["convites"]["comissao"];
                $campo["descricao"] = $pagamento["convites"]["descricao"];
                $campo["nome"] = "Penalização convite";
                $id_conta_corrente_linha = $db->Insert('conta_corrente_linhas', $campo);
            }

            if ($pagamento['divida']) {
                unset($campo);
                $campo['id_conta_corrente'] = $id_conta_corrente;
                $campo["valor"] = $pagamento["divida"];
                $campo["nome"] = "Dívida";
                $id_conta_corrente_linha = $db->Insert('conta_corrente_linhas', $campo);
            }
        }
        if ($pagamento['extras']) {
            foreach ($pagamento['extras']['items'] as $items) {
                $campo['id_conta_corrente'] = $id_conta_corrente;
                $campo["valor"] = $items['valor'];
                $campo["descricao"] = $items['descricao'];
                $campo["nome"] = "Extra";
                $id_conta_corrente_linha = $db->Insert('conta_corrente_linhas', $campo);
            }
        }
        $dbpagamentos->apagaExtras($id_rp);

        $_SESSION['sucesso'] = "Pagamento registado com sucesso.";
    } else {
        $_SESSION['erro'] = "Erro ao registar pagamento, o ocurreu um erro.";
    }
} else {
    $_SESSION['erro'] = "Erro ao registar pagamento, o pagamento já foi efectuado.";
}
header('Location: /administrador/index.php?pg=pagamentos&letra=.');
exit;
