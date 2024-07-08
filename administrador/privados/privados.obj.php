<?php
class privados
{
    function __construct($db)
    {
        $this->db = $db;
    }
    function listaVendasDiasTotal($data_evento = false)
    {
        if ($data_evento) {
            $where = " WHERE data_evento = '" . $data_evento . "' ";
        }
        $query = "SELECT count(*) as total, data_evento FROM venda_privados $where  GROUP BY data_evento DESC";
        $res_entradas = $this->db->query($query);
        return $res_entradas;
    }

    function verificaMesaVendida($id_mesa, $data_evento)
    {

        $query = "SELECT * FROM venda_privados WHERE id_mesa = $id_mesa AND data_evento = '" . $data_evento . "'  ORDER BY id ASC";
        $res = $this->db->query($query);
        if (!empty($res)) {
            return 1;
        }
    }
    function listaVendasGarrafasDiasTotal($data_evento = false)
    {
        if ($data_evento) {
            $where = " WHERE data_evento = '" . $data_evento . "' ";
        }
        $query = "SELECT count(*) as total, data_evento FROM venda_garrafas_bar $where  GROUP BY data_evento DESC";
        $res_entradas = $this->db->query($query);
        return $res_entradas;
    }


    function listaSalas($salas = array())
    {
        if ($salas) {
            $where = " AND privados_salas.id in(" . implode(',', $salas).")";
        }

        $query = "SELECT * FROM privados_salas WHERE activo = 1 $where ORDER BY id ASC";

        $res = $this->db->query($query);
        return $res;
    }
    function devolveSala($id)
    {

        $query = "SELECT * FROM privados_salas WHERE id = " . $id . " ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res[0];
    }

    function devolveMesa($id)
    {
        $query = "SELECT * FROM privados_salas_mesas WHERE id = " . $id . " ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res[0];
    }
    function listaMesas($id_sala = false)
    {
        if ($id_sala) {
            $where = " AND id_sala = " . $id_sala;
        }
        $query = "SELECT * FROM privados_salas_mesas WHERE 1=1 $where  ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res;
    }
    function listaBares($ids = false)
    {
        if ($ids) {
            $where = "WHERE id IN (" . implode(",", $ids) . ")";
        }
        $query = "SELECT * FROM bares $where ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res;
    }

    function devolveBar($id)
    {
        $query = "SELECT * FROM bares WHERE id = " . $id . "   ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res[0];
    }
    function listaGarrafas($ids = false)
    {
        if ($ids) {
            $where = "WHERE id IN (" . implode(",", $ids) . ")";
        }
        $query = "SELECT * FROM garrafas $where ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res;
    }

    function devolveGarrafa($id)
    {
        $query = "SELECT * FROM garrafas WHERE id = " . $id . "   ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res[0];
    }
    function listaVendaGarrafas($hoje = false, $data_evento = false)
    {
        if ($hoje) {
            if (date('H') < 14) {
                $data = date('Y-m-d', strtotime('-1 day'));
            } else {
                $data = date('Y-m-d');
            }
            $where = "WHERE data_evento = '" . $data . "'";
        }
        if ($data_evento) {
            $where = "WHERE data_evento = '" . $data_evento . "'";
        }
        $query = "SELECT venda_garrafas_bar.*, rps.nome as nome_rp, rps_processado.nome as nome_processado FROM venda_garrafas_bar INNER JOIN rps ON rps.id = venda_garrafas_bar.id_rp LEFT JOIN rps rps_processado ON rps_processado.id = venda_garrafas_bar.id_processado $where ORDER BY id DESC";
        $res = $this->db->query($query);

        foreach ($res as $i => $rs) {
            if ($rs['pagamento'] == 1) {
                $res[$i]['pagamento'] = "Multibanco";
            } else if ($rs['pagamento'] == 2) {
                $res[$i]['pagamento'] = "Dinheiro";
            }
        }
        return $res;
    }
    function totalVendaGarrafas($data_evento = false)
    {

        if ($data_evento) {
            $where = "WHERE data_evento = '" . $data_evento . "'";
        }

        $query = "SELECT SUM(venda_garrafas_bar.valor_multibanco) as valor_multibanco, SUM(venda_garrafas_bar.valor_dinheiro) as valor_dinheiro, SUM(venda_garrafas_bar.total) as total FROM venda_garrafas_bar $where ORDER BY id DESC";
        $res = $this->db->query($query);

        return $res[0];
    }
    function verificaPrivadoHash($hash = false)
    {
        $query = "SELECT COUNT(hash) as conta FROM venda_privados WHERE hash='$hash'";
        $res = $this->db->query($query);
        return $res[0]['conta'];
    }
    function verificaGarrafasHash($hash = false)
    {

        $query = "SELECT COUNT(hash) as conta FROM venda_garrafas_bar WHERE hash='$hash'";
        $res = $this->db->query($query);
        return $res[0]['conta'];
    }

    function devolveVendaGarrafas($id)
    {
        $query = "SELECT venda_garrafas_bar.*, rps.nome as nome_rp, rps_processado.nome as nome_processado , bares.nome as nome_bar FROM venda_garrafas_bar INNER JOIN rps ON rps.id = venda_garrafas_bar.id_rp LEFT JOIN rps rps_processado ON rps_processado.id = venda_garrafas_bar.id_processado LEFT JOIN bares ON bares.id = venda_garrafas_bar.id_bar WHERE venda_garrafas_bar.id = '" . $id . "' ORDER BY id DESC";
        $res = $this->db->query($query);
        foreach ($res as $i => $rs) {

            $res[$i]['garrafas'] = $this->devolveGarrafaVenda($rs['id']);;
            if ($rs['pagamento'] == 1) {
                $res[$i]['pagamento'] = "Multibanco";
            } else if ($rs['pagamento'] == 2) {
                $res[$i]['pagamento'] = "Dinheiro";
            }
        }
        return $res[0];
    }

    function devolveListaEsperaGarrafas($data_evento, $id = false)
    {

        if ($id > 0) {
            $where = "AND reserva_garrafas.id = '" . $id . "'";
        }

        $query = "SELECT reserva_garrafas.*, rps.nome as nome_rp, rps_processado.nome as nome_processado  FROM reserva_garrafas LEFT JOIN rps rps_processado ON reserva_garrafas.id_processado = rps_processado.id LEFT JOIN rps ON reserva_garrafas.id_rp = rps.id  WHERE reserva_garrafas.data_evento = '" . $data_evento . "' $where ORDER BY reserva_garrafas.id ASC";

        $res = $this->db->query($query);
        foreach ($res as $k => $rs) {

            $query = "SELECT * FROM reserva_garrafas_garrafas WHERE id_reserva = '" . $rs['id'] . "'";
            $res_garrafas = $this->db->query($query);
            $res[$k]['garrafas'] = $res_garrafas;
        }
        return $res;
    }

    function devolveGarrafaVenda($id)
    {
        $query = "SELECT venda_garrafas_bar_garrafas.quantidade, venda_garrafas_bar_garrafas.id_garrafa, garrafas.nome FROM venda_garrafas_bar_garrafas INNER JOIN garrafas ON garrafas.id = venda_garrafas_bar_garrafas.id_garrafa  WHERE venda_garrafas_bar_garrafas.id_compra = '" . $id . "' ORDER BY venda_garrafas_bar_garrafas.id ASC";
        return  $this->db->query($query);
    }

    function listaVendaPrivados($hoje = false, $data_evento = false)
    {

        if ($hoje) {

            if (date('H') < 14) {
                $data = date('Y-m-d', strtotime('-1 day'));
            } else {
                $data = date('Y-m-d');
            }
            $where = "WHERE data_evento = '" . $data . "'";
        }

        if ($data_evento) {
            $where = "WHERE data_evento = '" . $data_evento . "'";
        }

        $query = "SELECT venda_privados.*, rps.nome as nome_rp, rps_gerente.nome as nome_gerente, rps_processado.nome as nome_processado FROM venda_privados LEFT JOIN rps ON rps.id = venda_privados.id_rp LEFT JOIN rps rps_gerente ON rps_gerente.id = venda_privados.id_gerente LEFT JOIN rps rps_processado ON rps_processado.id = venda_privados.id_processado $where ORDER BY id DESC";
        $res = $this->db->query($query);
        foreach ($res  as $i => $rs) {

            $mesa = $this->devolveMesa($rs['id_mesa']);
            $sala = $this->devolveSala($mesa['id_sala']);
            $res[$i]['mesa'] = $mesa['codigo_mesa'];
            $res[$i]['sala'] = $sala['nome'];
            if ($rs['pagamento'] == 1) {
                $res[$i]['pagamento'] = "Multibanco";
            } else if ($rs['pagamento'] == 2) {
                $res[$i]['pagamento'] = "Dinheiro";
            }
        }
        return $res;
    }
    function totalVendaPrivados($data_evento = false)
    {

        if ($data_evento) {
            $where = "WHERE data_evento = '" . $data_evento . "'";
        }

        $query = "SELECT SUM(venda_privados.valor_multibanco_adiantado) as valor_multibanco_adiantado, SUM(venda_privados.valor_dinheiro_adiantado) as valor_dinheiro_adiantado,SUM(venda_privados.valor_mbway_adiantado) as valor_mbway_adiantado, SUM(venda_privados.valor_multibanco_adiantado + venda_privados.valor_dinheiro_adiantado+ venda_privados.valor_mbway_adiantado) as total_adiantado, SUM(venda_privados.valor_multibanco) as valor_multibanco, SUM(venda_privados.valor_dinheiro) as valor_dinheiro, SUM(venda_privados.valor_mbway) as valor_mbway, SUM(venda_privados.valor_multibanco + venda_privados.valor_dinheiro + venda_privados.valor_mbway) as total_evento, SUM(venda_privados.total) as total FROM venda_privados $where ORDER BY id DESC";
        $res = $this->db->query($query);

        return $res[0];
    }

    function devolveVendaPrivados($id)
    {
        $query = "SELECT venda_privados.*, rps.nome as nome_rp, rps_gerente.nome as nome_gerente, rps_processado.nome as nome_processado FROM venda_privados INNER JOIN rps ON rps.id = venda_privados.id_rp INNER JOIN rps rps_gerente ON rps_gerente.id = venda_privados.id_gerente LEFT JOIN rps rps_processado ON rps_processado.id = venda_privados.id_processado  WHERE venda_privados.id = '" . $id . " '  ORDER BY id ASC";
        $res = $this->db->query($query);
        foreach ($res as $i => $rs) {
            $mesa = $this->devolveMesa($rs['id_mesa']);
            $sala = $this->devolveSala($mesa['id_sala']);
            $res[$i]['mesa'] = $mesa['codigo_mesa'];
            $res[$i]['sala'] = $sala['nome'];

            $res[$i]['garrafas'] = $this->devolveGarrafaVendaPrivados($rs['id']);;
            if ($rs['pagamento'] == 1) {
                $res[$i]['pagamento'] = "Multibanco";
            } else if ($rs['pagamento'] == 2) {
                $res[$i]['pagamento'] = "Dinheiro";
            }
        }
        return $res[0];
    }
    function devolveGarrafaVendaPrivados($id)
    {
        $query = "SELECT venda_privados_garrafas.quantidade, venda_privados_garrafas.id_garrafa, garrafas.nome FROM venda_privados_garrafas INNER JOIN garrafas ON garrafas.id = venda_privados_garrafas.id_garrafa  WHERE venda_privados_garrafas.id_compra = '" . $id .  "' ORDER BY venda_privados_garrafas.id ASC";
        return  $this->db->query($query);
    }

    function devolveGarrafaVendaPrivadosData($data_evento)
    {
        $query = "SELECT SUM(venda_privados_garrafas.quantidade) as quantidade, venda_privados_garrafas.id_garrafa, garrafas.nome FROM venda_privados_garrafas INNER JOIN garrafas ON garrafas.id = venda_privados_garrafas.id_garrafa INNER JOIN venda_privados ON venda_privados.id = venda_privados_garrafas.id_compra  WHERE venda_privados.data_evento = '" . $data_evento .  "' GROUP BY venda_privados_garrafas.id_garrafa ORDER BY venda_privados_garrafas.id ASC";
        return  $this->db->query($query);
    }

    function devolveGarrafaVendaGarrafasData($data_evento)
    {
        $query = "SELECT SUM(venda_garrafas_bar_garrafas.quantidade) as quantidade, venda_garrafas_bar_garrafas.id_garrafa, garrafas.nome FROM venda_garrafas_bar_garrafas INNER JOIN garrafas ON garrafas.id = venda_garrafas_bar_garrafas.id_garrafa INNER JOIN venda_garrafas_bar ON venda_garrafas_bar.id = venda_garrafas_bar_garrafas.id_compra  WHERE venda_garrafas_bar.data_evento = '" . $data_evento .  "' GROUP BY venda_garrafas_bar_garrafas.id_garrafa ORDER BY venda_garrafas_bar_garrafas.id ASC";
        return  $this->db->query($query);
    }

    function devolveReservaDataEvento($id_mesa, $data_evento)
    {
        $query = "SELECT *, cartoes as numero_cartoes, nome as nome_cliente FROM privados_salas_mesas_disponibilidade WHERE id_mesa = $id_mesa AND data_evento = '" . $data_evento . "'  ORDER BY id ASC";

        $res = $this->db->query($query);

        if ($res[0]['id'] > 0) {
            // $query = "SELECT venda_privados.id FROM venda_privados WHERE venda_privados.id_reserva = '" . $res[0]['id'] .  "'";
            // $res_reservas = $this->db->query($query);
            // if(empty($res_reservas)){
            return $res[0];
            // }
        }
    }
    function permiteApagar()
    {
        $permite = 0;

        if ($_SESSION['id_utilizador']) {
            $query = "SELECT tipo FROM administradores WHERE id = " . $_SESSION['id_utilizador'];
            $res = $this->db->query($query);
            if ($res[0]['tipo'] == 1) {
                $permite = 1;
            }
        }

        if ($_SESSION['id_processado']) {
            $query = "SELECT permite_apagar_privados FROM rps WHERE id = " . $_SESSION['id_processado'];
            $res = $this->db->query($query);
            if ($res[0]['permite_apagar_privados'] == 1) {
                $permite = 1;
            }
        }
        return $permite;
    }
}
