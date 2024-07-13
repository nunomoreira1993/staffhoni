<?php
class estatisticas {
	private $db;
    private $data_evento;
    function __construct($db) {
		$this->db = $db;

        if (date('H') < 14) {
            $this->data_evento =  date('Y-m-d', strtotime('-1 day'));
        }
        else {
            $this->data_evento = date('Y-m-d');
        }
    }
    function syncToStatistics() {
        #entradas rp dia
        $query = "SELECT rps_entradas.data_evento FROM rps_entradas LEFT JOIN estatisticas_entradas_rp ON rps_entradas.data_evento = estatisticas_entradas_rp.data_evento AND estatisticas_entradas_rp.realtime = 0 WHERE estatisticas_entradas_rp.data_evento IS NULL AND rps_entradas.data_evento <  '" . $this->data_evento . "' GROUP BY rps_entradas.data_evento ORDER BY rps_entradas.data_evento DESC";
        $res = $this->db->query($query);

        #add data_evento
        $data_array = array("data_evento" => $this->data_evento);
        $res[] = $data_array;

        foreach($res as $rs) {
            $data_evento = $rs["data_evento"];

            #statistic by day (RP)
            $statistic_date = $this->simulateStatisticByEntranceRPDay($data_evento);
            if($statistic_date) {
                $this->db->query("DELETE FROM estatisticas_entradas_rp WHERE data_evento = '" . $data_evento . "'");
                foreach($statistic_date as $k => $stat) {
                    $insert = array();
                    $insert["posicao"] = $k + 1;
                    $insert["data_evento"] = $data_evento;
                    $insert["entradas"] = $stat["entradas"];
                    $insert["id_rp"] = $stat["id_rp"];

                    if($data_evento == $this->data_evento) {
                        $insert["realtime"] = 1;
                    }
                    else {
                        $insert["realtime"] = 0;
                    }
                    $this->db->insert("estatisticas_entradas_rp", $insert);
                }
            }

            #statistic by day (Chefe)
            $statistic_date = $this->simulateStatisticByEntranceTeamDay($data_evento);
            if($statistic_date) {
                $this->db->query("DELETE FROM estatisticas_entradas_chefe WHERE data_evento = '" . $data_evento . "'");
                foreach($statistic_date as $k => $stat) {
                    $insert = array();
                    $insert["posicao"] = $k + 1;
                    $insert["data_evento"] = $data_evento;
                    $insert["entradas"] = $stat["entradas"];
                    $insert["id_rp"] = $stat["id_chefe_equipa"];

                    if($data_evento == $this->data_evento) {
                        $insert["realtime"] = 1;
                    }
                    else {
                        $insert["realtime"] = 0;
                    }

                    $this->db->insert("estatisticas_entradas_chefe", $insert);
                }
            }
        }


        #privados rp dia
        $query = "SELECT venda_privados.data_evento FROM venda_privados LEFT JOIN estatisticas_privados_dia_rp ON venda_privados.data_evento = estatisticas_privados_dia_rp.data_evento AND estatisticas_privados_dia_rp.realtime = 0 WHERE estatisticas_privados_dia_rp.data_evento IS NULL AND venda_privados.data_evento <  '" . $this->data_evento . "'  GROUP BY venda_privados.data_evento ORDER BY venda_privados.data_evento DESC";
        $res = $this->db->query($query);
        $res[] = $data_array;

        foreach($res as $rs) {
            $data_evento = $rs["data_evento"];

            #privados statistic by day (Chefe)
            $statistic_privados_rp = $this->simulateStatisticByPrivadosRPDay($data_evento);
            if($statistic_privados_rp) {
                $this->db->query("DELETE FROM estatisticas_privados_dia_rp WHERE data_evento = '" . $data_evento . "'");
                foreach($statistic_privados_rp as $k => $stat) {
                    $insert = array();
                    $insert["posicao"] = $k + 1;
                    $insert["data_evento"] = $data_evento;
                    $insert["total"] = $stat["total"];
                    $insert["id_rp"] = $stat["id_rp"];

                    if($data_evento == $this->data_evento) {
                        $insert["realtime"] = 1;
                    }
                    else {
                        $insert["realtime"] = 0;
                    }

                    $this->db->insert("estatisticas_privados_dia_rp", $insert);
                }
            }
        }

        #privados rp weekly
        $query = "SELECT semana_ate FROM estatisticas_privados_semana_rp WHERE realtime = 0 ORDER BY semana_ate DESC LIMIT 1";
        $res_semana = $this->db->query($query);

        if(empty($res_semana)) {
            $query = "SELECT data_evento FROM estatisticas_privados_dia_rp GROUP BY data_evento ORDER BY data_evento ASC";
            $res = $this->db->query($query);
            $semana["end_date"] = "2020-01-01";
        }
        else {
            $query = "SELECT data_evento FROM estatisticas_privados_dia_rp WHERE data_evento > '" . $res_semana[0]["semana_ate"] . "' GROUP BY data_evento ORDER BY data_evento ASC";
            $res = $this->db->query($query);
            $semana["end_date"] = $res_semana["semana_ate"];
        }

        foreach($res as $rs) {
            $data_evento = $rs["data_evento"];
            if($data_evento > $semana["end_date"]) {
                $semana = getWeekInfo($data_evento);

                #delete all of event date
                $query = "DELETE FROM estatisticas_privados_semana_rp WHERE semana = " . $semana["week"];
                $this->db->query($query);

                $query = "SELECT SUM(total) as total, id_rp  FROM estatisticas_privados_dia_rp WHERE (data_evento >= '" . $semana["start_date"] . "' AND data_evento <= '" . $semana["end_date"] . "') GROUP BY id_rp ORDER BY total DESC";
                $evento = $this->db->query($query);
                foreach($evento as $k => $event) {
                    $insert = array();
                    $insert["posicao"] = $k + 1;
                    $insert["total"] = $event["total"];
                    $insert["id_rp"] = $event["id_rp"];
                    $insert["semana"] = $semana["week"];
                    $insert["semana_de"] = $semana["start_date"];
                    $insert["semana_ate"] = $semana["end_date"];
                    $insert["pago"] = $stat["pago"];
                    if($this->data_evento >= $semana["start_date"] && $this->data_evento <= $semana["end_date"] ) {
                        $insert["realtime"] = 1;
                    }
                    else {
                        $insert["realtime"] = 0;
                    }

                    $this->db->insert("estatisticas_privados_semana_rp", $insert);
                }
            }

        }

    }

    function simulateStatisticByEntranceRPDay($data_evento) {
        $query = "SELECT SUM(rps_entradas.quantidade) as entradas, rps_entradas.id_rp FROM rps_entradas INNER JOIN rps ON rps.id = rps_entradas.id_rp AND rps.comissao_guest = 1 WHERE rps_entradas.data_evento = '" . $data_evento . "' GROUP BY rps_entradas.id_rp ORDER BY entradas DESC";
        $res = $this->db->query($query);
        return $res;
    }
    function getStatisticByEntranceRPDay($data_evento, $limit = "") {

        $query = "SELECT COUNT(*) as conta FROM estatisticas_entradas_rp INNER JOIN rps ON rps.id = estatisticas_entradas_rp.id_rp WHERE estatisticas_entradas_rp.data_evento = '" . $data_evento . "' ORDER BY estatisticas_entradas_rp.entradas DESC";
        $res_conta = $this->db->query($query);

        $query = "SELECT estatisticas_entradas_rp.posicao, estatisticas_entradas_rp.entradas, estatisticas_entradas_rp.id_rp, rps.nome, estatisticas_entradas_rp.data_evento FROM estatisticas_entradas_rp INNER JOIN rps ON rps.id = estatisticas_entradas_rp.id_rp WHERE estatisticas_entradas_rp.data_evento = '" . $data_evento . "' ORDER BY estatisticas_entradas_rp.entradas DESC $limit";
        $res = $this->db->query($query);

        return array("count" => $res_conta[0]["conta"], "result" => $res);
    }
    function simulateStatisticByEntranceTeamDay($data_evento) {
        $query = "SELECT SUM(rps_entradas.quantidade) as entradas, rps.id_chefe_equipa FROM rps_entradas INNER JOIN rps ON rps.id = rps_entradas.id_rp AND rps.comissao_guest = 1 WHERE rps_entradas.data_evento = '" . $data_evento . "' AND rps.id_chefe_equipa > 0 GROUP BY rps.id_chefe_equipa ORDER BY entradas DESC";
        $res = $this->db->query($query);
        return $res;
    }
    function getStatisticByEntranceTeamDay($data_evento, $limit = "") {

        $query = "SELECT COUNT(*) as conta FROM estatisticas_entradas_chefe INNER JOIN rps ON rps.id = estatisticas_entradas_chefe.id_rp WHERE estatisticas_entradas_chefe.data_evento = '" . $data_evento . "' ORDER BY estatisticas_entradas_chefe.entradas DESC";
        $res_conta = $this->db->query($query);

        $query = "SELECT estatisticas_entradas_chefe.posicao, estatisticas_entradas_chefe.entradas, estatisticas_entradas_chefe.id_rp, rps.nome, estatisticas_entradas_chefe.data_evento FROM estatisticas_entradas_chefe INNER JOIN rps ON rps.id = estatisticas_entradas_chefe.id_rp WHERE estatisticas_entradas_chefe.data_evento = '" . $data_evento . "' ORDER BY estatisticas_entradas_chefe.entradas DESC $limit";
        $res = $this->db->query($query);

        return array("count" => $res_conta[0]["conta"], "result" => $res);
    }
    function getStatisticByPrivadosRPDay($data_evento) {
        $query = "SELECT estatisticas_privados_dia_rp.posicao, estatisticas_privados_dia_rp.total, estatisticas_privados_dia_rp.id_rp, rps.nome, estatisticas_privados_dia_rp.data_evento FROM estatisticas_privados_dia_rp INNER JOIN rps ON rps.id = estatisticas_privados_dia_rp.id_rp WHERE estatisticas_privados_dia_rp.data_evento = '" . $data_evento . "' ORDER BY estatisticas_privados_dia_rp.entradas DESC";
        $res = $this->db->query($query);
        return $res;
    }
    function simulateStatisticByPrivadosRPDay($data_evento) {
        $query = "SELECT count(f.data_evento) as quantidade, f.id_mesa as id_mesa, SUM(f.total) as total, f.data_evento, f.id_cargo, f.id_rp FROM (SELECT venda_privados.id as id, rps.id as id_rp, count(venda_privados.data_evento) as quantidade, venda_privados.id_mesa as id_mesa, (privados_salas_mesas_disponibilidade.valor) as total, venda_privados.data_evento as data_evento, rps.id_cargo FROM venda_privados INNER JOIN rps ON rps.id = venda_privados.id_rp INNER JOIN privados_salas_mesas_disponibilidade ON venda_privados.id_reserva = privados_salas_mesas_disponibilidade.id WHERE venda_privados.data_evento = '" . $data_evento . "' GROUP BY venda_privados.data_evento, venda_privados.id_mesa, venda_privados.id_rp) f  GROUP BY f.id_rp  ORDER BY total DESC";
        $res = $this->db->query($query);
        return $res;
    }

    function getDaysStatisticByPrivadosRPWeekly($limit) {

        $query = "SELECT count(*) as conta FROM (SELECT estatisticas_privados_semana_rp.semana FROM estatisticas_privados_semana_rp GROUP BY estatisticas_privados_semana_rp.semana ORDER BY estatisticas_privados_semana_rp.semana DESC) estatisticas_privados_semana_rp_replace ";
        $res_conta = $this->db->query($query);

        $query = "SELECT estatisticas_privados_semana_rp.semana, estatisticas_privados_semana_rp.semana_de, estatisticas_privados_semana_rp.semana_ate FROM estatisticas_privados_semana_rp  GROUP BY estatisticas_privados_semana_rp.semana ORDER BY estatisticas_privados_semana_rp.semana DESC $limit";
        $res = $this->db->query($query);

        return array("count" => $res_conta[0]["conta"], "result" => $res);
    }

    function getStatisticByPrivadosRPWeeklyByWeek($week, $limit) {

        $query = "SELECT COUNT(*) as conta FROM estatisticas_privados_semana_rp INNER JOIN rps ON rps.id = estatisticas_privados_semana_rp.id_rp WHERE estatisticas_privados_semana_rp.semana = $week   ORDER BY estatisticas_privados_semana_rp.posicao ASC ";
        $res_conta = $this->db->query($query);

        $query = "SELECT estatisticas_privados_semana_rp.posicao, estatisticas_privados_semana_rp.total, rps.nome FROM estatisticas_privados_semana_rp INNER JOIN rps ON rps.id = estatisticas_privados_semana_rp.id_rp  WHERE estatisticas_privados_semana_rp.semana = $week  ORDER BY estatisticas_privados_semana_rp.posicao ASC $limit";
        $res = $this->db->query($query);

        return array("count" => $res_conta[0]["conta"], "result" => $res);
    }
    function getDaysStatisticByEntranceRPDay($limit) {

        $query = "SELECT count(*) as conta FROM (SELECT estatisticas_entradas_rp.data_evento FROM estatisticas_entradas_rp GROUP BY estatisticas_entradas_rp.data_evento ORDER BY estatisticas_entradas_rp.data_evento DESC) estatisticas_entradas_rp_replace ";
        $res_conta = $this->db->query($query);

        $query = "SELECT estatisticas_entradas_rp.data_evento FROM estatisticas_entradas_rp GROUP BY estatisticas_entradas_rp.data_evento ORDER BY estatisticas_entradas_rp.data_evento DESC $limit";
        $res = $this->db->query($query);

        return array("count" => $res_conta[0]["conta"], "result" => $res);
    }
    function getDaysStatisticByEntranceTeamDay($limit) {

        $query = "SELECT count(*) as conta FROM (SELECT estatisticas_entradas_chefe.data_evento FROM estatisticas_entradas_chefe  GROUP BY estatisticas_entradas_chefe.data_evento ORDER BY estatisticas_entradas_chefe.data_evento DESC) estatisticas_entradas_chefe_replace ";
        $res_conta = $this->db->query($query);

        $query = "SELECT estatisticas_entradas_chefe.data_evento FROM estatisticas_entradas_chefe  GROUP BY estatisticas_entradas_chefe.data_evento ORDER BY estatisticas_entradas_chefe.data_evento DESC $limit";
        $res = $this->db->query($query);

        return array("count" => $res_conta[0]["conta"], "result" => $res);
    }
}
