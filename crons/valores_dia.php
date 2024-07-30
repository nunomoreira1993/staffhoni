<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";


require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);

$query = "SELECT data_evento FROM rps_entradas  GROUP BY data_evento DESC";
$eventos = $db->query($query);

if ($eventos) {
    foreach ($eventos as $k => $evento) {

        $query = "SELECT id FROM rps";
        $rps = $db->query($query);

        foreach($rps as $rp) {
            $eventos_return[$k]['data_evento'] = $evento['data_evento'];

            $guest = $dbpagamentos->listaEventosRP($rp["id"], $evento['data_evento']);
            if($guest) {
                $eventos_return[$k]['entradas'] += $guest['entrou'];
                $eventos_return[$k]['entradas_comissao'] += $guest['comissao'];
                $eventos_return[$k]['entradas_bonus'] += $guest['comissao_bonus'];
            }


            $guest_team = $dbpagamentos->listaEventosEquipaRP($rp["id"], $evento['data_evento']);
            if ($guest_team) {
                $eventos_return[$k]['entradas_equipa'] += $guest_team['entrou'];
                $eventos_return[$k]['entradas_equipa_comissao'] += $guest_team['comissao'];
                $eventos_return[$k]['entradas_equipa_comissao_bonus'] += $guest_team['comissao_bonus'];
            }

            $privados = $dbpagamentos->devolveComissaoPrivados($rp["id"], $evento['data_evento']);
            if($privados) {
                $eventos_return[$k]['privados_numero'] +=  $privados["numero"];
                $eventos_return[$k]['privados_total'] += $privados["total"];
                $eventos_return[$k]['privados_comissao'] += $privados["comissao"];
            }

            $privados_chefe = $dbpagamentos->devolveComissaoPrivadosChefe($rp["id"], $evento['data_evento']);
            if($privados_chefe) {
                $eventos_return[$k]['privados_equipa_numero'] +=  $privados_chefe["numero"];
                $eventos_return[$k]['privados_equipa_total'] += $privados_chefe["total"];
                $eventos_return[$k]['privados_equipa_comissao'] += $privados_chefe["comissao"];
            }

            if((int) $eventos_return[$k]['entradas'] == 0 && (int) $eventos_return[$k]['privados_numero'] == 0 && (int) $eventos_return[$k]['entradas_equipa'] == 0 && (int) $eventos_return[$k]['privados_equipa_numero'] == 0){
                unset($eventos_return[$k]);
            }
            if($eventos_return[$k]) {
                $db->query("DELETE FROM eventos_totais WHERE data_evento = '" . $eventos_return[$k]["data_evento"] . "'");
                $db->insert("eventos_totais", $eventos_return[$k]);
            }
        }
    }
}


pr($eventos_return);

