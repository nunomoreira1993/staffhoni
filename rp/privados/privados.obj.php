<?php
class privados {
	function __construct($db) {
		$this->db = $db;
	}
	function devolveIDPrivado() {

		$query = "SELECT * FROM administradores WHERE tipo = 3 ORDER BY id ASC";
		$res = $this->db->query($query);

		return $res[0]['id'];
	}
	function listaSalas($salas = array()) {
		if (!empty($salas)) {
			$where = "AND id IN ('" . implode("','", $salas) . "') ";
		}

		$query = "SELECT * FROM privados_salas WHERE activo = 1 $where ORDER BY id ASC";
		$res = $this->db->query($query);

		return $res;
	}

	function listaGarrafas($ids = array()) {
		if (!empty($ids)) {
			$where = "WHERE id IN (" . implode(",", $ids) . ")";
		}
		$query = "SELECT * FROM garrafas $where ORDER BY id ASC";
		$res = $this->db->query($query);
		return $res;
	}

	function devolveSala($id) {

		$query = "SELECT * FROM privados_salas WHERE id = " . $id . " ORDER BY id ASC";
		$res = $this->db->query($query);
		return $res[0];
	}
	function devolveListaEspera($data_evento, $id = false) {
		if ($id > 0) {
			$where = "AND id = '" . $id . "'";
		}
		$query = "SELECT * FROM venda_privados_lista_espera WHERE data_evento = '" . $data_evento . "' $where ORDER BY id ASC";
		$res = $this->db->query($query);

		return $res;
	}
	function devolveListaEsperaGarrafas($data_evento, $id = false) {
		if ($id > 0) {
			$where = "AND id = '" . $id . "'";
		}

		$query = "SELECT * FROM reserva_garrafas WHERE data_evento = '" . $data_evento . "' $where ORDER BY id ASC";
		$res = $this->db->query($query);
		foreach ($res as $k => $rs) {

			$query = "SELECT * FROM reserva_garrafas_garrafas WHERE id_reserva = '" . $rs['id'] . "'";
			$res_garrafas = $this->db->query($query);
			$res[$k]['garrafas'] = $res_garrafas;
		}
		return $res;
	}
	function devolveMesa($id) {
		$query = "SELECT * FROM privados_salas_mesas WHERE id = " . $id . " ORDER BY id ASC";
		$res = $this->db->query($query);
		return $res[0];
	}
	function listaMesas($id_sala = false, $mesas = array(), $nome = false, $data_evento = false) {
		if ($id_sala) {
			$where = " AND id_sala = " . $id_sala;
		}

		if (!empty($mesas)) {
			$where = " AND id IN ('" . implode( "','", $mesas) . "') ";
		}

        if($nome != ""){
            $inner = " INNER JOIN privados_salas_mesas_disponibilidade ON privados_salas_mesas_disponibilidade.id_mesa = privados_salas_mesas.id ";
            $where = " AND privados_salas_mesas_disponibilidade.nome like '%" . $nome. "%' AND privados_salas_mesas_disponibilidade.data_evento = '".$data_evento."'";
        }

		$query = "SELECT * FROM privados_salas_mesas  $inner  WHERE 1=1 $where  ORDER BY privados_salas_mesas.id ASC";
		$res = $this->db->query($query);


		return $res;
	}
	function verificaMesaDisponivel($id_mesa, $data_evento) {

		$query = "SELECT * FROM privados_salas_mesas_disponibilidade WHERE id_mesa = $id_mesa AND data_evento = '" . $data_evento . "'  ORDER BY id ASC";
		$res = $this->db->query($query);
		if (empty($res)) {
			return 1;
		}
	}
	function verificaMesaVendida($id_mesa, $data_evento) {

		$query = "SELECT * FROM venda_privados WHERE id_mesa = $id_mesa AND data_evento = '" . $data_evento . "'  ORDER BY id ASC";
		$res = $this->db->query($query);
		if (!empty($res)) {
			return $res;
		}
	}
	function devolveVenda($id_venda) {

		$query = "SELECT * FROM venda_privados WHERE id = '" . (int) $id_venda . "'";
		$res = $this->db->query($query);
		if (!empty($res)) {
			return $res[0];
		}
	}
	function devolveReservaMesa($id_mesa, $data_evento) {
		$query = "SELECT * FROM privados_salas_mesas_disponibilidade  WHERE id_mesa = $id_mesa AND data_evento = '" . $data_evento . "'  ORDER BY id ASC";
		$res = $this->db->query($query);
		return $res[0];
	}

	function devolveSalasPesquisa($nome = false, $data_evento = false) {
		if ($nome && $data_evento) {
			$query = "SELECT privados_salas_mesas.id_sala FROM privados_salas_mesas_disponibilidade INNER JOIN privados_salas_mesas ON privados_salas_mesas.id = privados_salas_mesas_disponibilidade.id_mesa  WHERE privados_salas_mesas_disponibilidade.nome like '%" . $nome . "%' AND privados_salas_mesas_disponibilidade.data_evento = '" . $data_evento . "'  GROUP BY privados_salas_mesas.id_sala";
			$res = $this->db->query($query);

			return array_column($res, 'id_sala');
		}
	}

	function devolveMesasPesquisa($nome = false, $data_evento = false) {
		if ($nome && $data_evento) {
			$query = "SELECT id_mesa FROM privados_salas_mesas_disponibilidade  WHERE nome like '%" . $nome . "%' AND data_evento = '" . $data_evento . "'    GROUP BY id_mesa";
			$res = $this->db->query($query);
			return array_column($res, 'id_mesa');
		}
	}

	function devolveReservasMesas($data_evento) {
		$query = "SELECT privados_salas_mesas.codigo_mesa, privados_salas_mesas_disponibilidade.* FROM privados_salas_mesas_disponibilidade  INNER JOIN privados_salas_mesas ON privados_salas_mesas.id = privados_salas_mesas_disponibilidade.id_mesa  WHERE privados_salas_mesas_disponibilidade.data_evento = '" . $data_evento . "'  ORDER BY privados_salas_mesas.id_sala ASC, privados_salas_mesas.id ASC";
		$res = $this->db->query($query);

		return $res;
	}
	function devolveReserva($id) {
		$query = "SELECT * FROM privados_salas_mesas_disponibilidade WHERE id = $id  LIMIT 1";
		$res = $this->db->query($query);
		return $res[0];
	}
	function devolveProximoPrivado() {
		if (date('H') < 14) {
			$data_evento = date('Y-m-d', strtotime('-1 day'));
		} else {
			$data_evento = date('Y-m-d');
		}
		$query = "SELECT * FROM privados_salas_mesas_disponibilidade WHERE data_evento >= '" . $data_evento . "'  ORDER BY data_evento ASC LIMIT 1";
		$res = $this->db->query($query);

		return $res[0];
	}
	function smsto($params) {
		$post['to'] = array($params['telemovel']);
		$post['text'] = $params['mensagem'];
		$post['from'] = "HONI";
		$post['coding'] = "gsm-pt";
		$post['parts'] = 4;
		$user = "honi";
		$password = "NUgm17?%";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://dashboard.wausms.com/Api/rest/message");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/json",
			"Authorization: Basic " . base64_encode($user . ":" . $password)
		));
		$result = curl_exec($ch);
		curl_close($ch);

		if ($result) {
			return json_decode($result);
		}
	}
	function getMensagemDefault() {
		$mensagem = "Olá {NOME}!\nObrigado por reservares uma mesa connosco para o dia {DATA}, esperemos que te divirtas e aproveites o Sunset!\nDeixamos aqui os dados para efetuares um pagamento de {VALOR}€ (sinalização de 50% da tua reserva):\nIBAN: PT50 0000 0000 0000 0000 0000 0\nMBWAY: 900 000 000\nTens 2 formas de o fazer (sempre que possível através do MBWAY), pedimos apenas que na descrição coloques o nome da reserva em questão! Caso não faças a sinalização nas próximas 48h, a tua reserva irá ser cancelada.\nObrigado e até já!\nHONI";
		return $mensagem;
	}

    function devolveOcupacaoMesa($id_mesa, $data_evento)
    {
        $query = "SELECT * FROM privados_salas_mesas_ocupacao WHERE id_mesa = $id_mesa AND data_evento = '" . $data_evento . "'  ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res[0];
    }
    function verificaMesaOcupada($id_mesa, $data_evento){
        $query = "SELECT * FROM privados_salas_mesas_ocupacao WHERE id_mesa = $id_mesa AND data_evento = '" . $data_evento . "'  ORDER BY id ASC";
        $res = $this->db->query($query);
        if (!empty($res)) {
            return $res[0];
        }
    }
}
