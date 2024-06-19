<?php
class rps {
	const id_chefe_equipa = 20;
	const id_produtor = 21;

	function __construct($db) {
		$this->db = $db;
	}
	function getIdChefeEquipa() {
		return self::id_chefe_equipa;
	}
	function getIdProdutor() {
		return self::id_produtor;
	}
	function verificaRP($username, $password) {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/validacao/validacao.obj.php');
		$dbvalidacao = new validacao($db);
		$erro = $dbvalidacao->valida_email($username, "E-mail");
		if ($erro) {
			$erroTel = $dbvalidacao->valida_inteiro($username, "Télemovel");
			if ($erroTel) {
				$erro = "Por favor digite um e-mail válido ou um número de telemóvel válido para iniciar sessão.";
			} else {
				unset($erro);
			}
		}

		if (empty($erro)) {
			$password = trim($password);
			$password = base64_encode($password);
			$query = "SELECT count(*) as conta FROM rps WHERE (rps.email = '" . $username . "' OR rps.telemovel ='" . $username . "') and rps.password = '$password' ";
			$res = $this->db->query($query);
			if ($res[0]['conta'] == 0) {
				$erro = "Os dados não estão correctos";
			}
		}
		return $erro;
	}
	function setSession($login, $password) {
		$password = trim($password);
		$password = base64_encode($password);

		$query = "SELECT * FROM rps WHERE (rps.email = '" . $login . "' OR rps.telemovel ='" . $login . "') and rps.password = '$password' ";
		$res = $this->db->query($query);
		$_SESSION['id_rp'] = $res[0]['id'];
		return $res;
	}
	function listaCargos() {
		$query = "SELECT * FROM rps_cargos ORDER BY id ASC";
		$res = $this->db->query($query);
		return $res;
	}
	function devolveRP($id) {
		$query = "SELECT * FROM rps WHERE rps.id = '$id'";
		$res = $this->db->query($query);
		return $res[0];
	}
	function getRPByQRCode($qrcode) {
		$query = "SELECT * FROM rps WHERE rps.qrcode = '$qrcode'";
		$res = $this->db->query($query);
		return $res[0];
	}
	function listaRPs($letra = false, $entradas = false, $gerente = false, $tipos = false, $filtros = false, $limit = false) {
		$where = "";
		if ($filtros) {
			if ($filtros['nome']) {
				$where .= " AND rps.nome like '%" . $filtros['nome'] . "%'";
			}
			if ($filtros['telemovel']) {

				$where .= " AND rps.telemovel like '" . $filtros['telemovel'] . "%'";
			}
		}
		if ($letra) {
			$where .= " AND rps.nome like '" . $letra . "%'";
		}
		if ($entradas) {
			if (date('H') < 14) {
				$data = date('Y-m-d', strtotime('-1 day'));
				$where_entradas = "AND data_evento = '" . $data . "'";
			} else {
				$data = date('Y-m-d');
				$where_entradas = "AND data_evento = '" . $data . "'";
			}

			$join = " LEFT JOIN rps_cargos ON rps.id_cargo = rps_cargos.id ";
			$campos = ", rps_cargos.nome as cargo";
		}
		if ($gerente) {
			$where .= " AND rps.id_cargo = " . $gerente;
		}

		if ($tipos) {
			$where .= " AND rps.id_cargo in (" . implode(',', $tipos) . ")";
		}

		$query = "SELECT rps.* $campos FROM rps $join WHERE 1=1 $where ORDER BY nome $limit";

		$res = $this->db->query($query);
		if ($entradas == 1) {
			foreach ($res as $k => $rs) {
				$id = $rs['id'];
				$query = "SELECT SUM(quantidade) as conta FROM rps_entradas WHERE id_rp =  " . $id . " $where_entradas";

				$res_entradas = $this->db->query($query);
				$res[$k]['entradas'] = intval($res_entradas[0]['conta']);
			}
		}
		return $res;
	}
	function listaNumeroRPS($letra = false, $entradas = false, $gerente = false, $tipos = false, $filtros = false) {
		$where = "";

		if ($filtros) {
			if ($filtros['nome']) {
				$where .= " AND rps.nome like '%" . $filtros['nome'] . "%'";
			}
			if ($filtros['telemovel']) {

				$where .= " AND rps.telemovel like '" . $filtros['telemovel'] . "%'";
			}
		}

		if ($letra) {
			$where .= " AND rps.nome like '" . $letra . "%'";
		}
		if ($entradas) {
			if (date('H') < 14) {
				$data = date('Y-m-d', strtotime('-1 day'));
				$where_entradas = "AND data_evento = '" . $data . "'";
			} else {
				$data = date('Y-m-d');
				$where_entradas = "AND data_evento = '" . $data . "'";
			}

			$join = " LEFT JOIN rps_cargos ON rps.id_cargo = rps_cargos.id ";
			$campos = ", rps_cargos.nome as cargo";
		}
		if ($gerente) {
			$where .= " AND rps.id_cargo = " . $gerente;
		}

		if ($tipos) {
			$where .= " AND rps.id_cargo in (" . implode(',', $tipos) . ")";
		}

		$query = "SELECT count(*) as conta $campos FROM rps $join WHERE 1=1 $where ORDER BY nome ";
		$res = $this->db->query($query);

		return $res[0]['conta'];
	}

	function listaIniciaisRPs($entradas = false, $gerente = false, $tipos = false) {
		$where = "";
		if ($entradas) {
			if (date('H') < 14) {
				$data = date('Y-m-d', strtotime('-1 day'));
				$where_entradas = "AND data_evento = '" . $data . "'";
			} else {
				$data = date('Y-m-d');
				$where_entradas = "AND data_evento = '" . $data . "'";
			}

			$join = " LEFT JOIN rps_cargos ON rps.id_cargo = rps_cargos.id ";
			$campos = ", rps_cargos.nome as cargo";
		}
		if ($gerente) {
			$where .= " AND rps.id_cargo = " . $gerente;
		}

		if ($tipos) {
			$where .= " AND rps.id_cargo in (" . implode(',', $tipos) . ")";
		}

		$query = "SELECT DISTINCT LEFT(rps.nome, 1) as letra FROM rps $join WHERE 1=1 $where ORDER BY rps.nome";
		$res = $this->db->query($query);

		return $res;
	}
	function contaRps() {
		$query = "SELECT count(*) as conta FROM rps";
		$res = $this->db->query($query);
		return $res[0]['conta'];
	}
	function listaEntradasDia() {

		if (date('H') < 14) {
			$data = date('Y-m-d', strtotime('-1 day'));
			$where_entradas = " data_evento = '" . $data . "'";
		} else {
			$data = date('Y-m-d');
			$where_entradas = " data_evento = '" . $data . "'";
		}

		$query = "SELECT rps_entradas.*, rps.nome FROM rps_entradas LEFT JOIN rps ON rps_entradas.id_rp = rps.id WHERE  $where_entradas ORDER by rps_entradas.data DESC LIMIT 50";

		$res_entradas = $this->db->query($query);
		return $res_entradas;
	}

	function listaEntradasData($data) {

		$query = "SELECT rps_entradas.*, rps.nome FROM rps_entradas LEFT JOIN rps ON rps_entradas.id_rp = rps.id WHERE data_evento = '" . $data . "' ORDER by rps_entradas.data DESC";

		$res_entradas = $this->db->query($query);
		return $res_entradas;
	}

	function devolveEntrada($id) {
		$query = "SELECT * FROM rps_entradas  WHERE  id = " . $id . "";
		$res_entradas = $this->db->query($query);
		return $res_entradas[0];
	}
	function listaEntradasDiasRP($id) {
		$query = "SELECT sum(quantidade) as quantidade, data_evento FROM rps_entradas  WHERE  id_rp = " . $id . " GROUP BY data_evento DESC";
		$res_entradas = $this->db->query($query);
		return $res_entradas;
	}
	function listaEntradasDiasTotal($data_evento = false, $filtros = false, $limit = false) {

		if ($filtros['data_evento']) {
			$data_evento = $filtros['data_evento'];
		}
		if ($data_evento) {
			$where = " WHERE data_evento = '" . $data_evento . "' ";
		}
		$query = "SELECT sum(quantidade) as total, data_evento FROM rps_entradas $where  GROUP BY data_evento DESC $limit";
		$res_entradas = $this->db->query($query);
		foreach ($res_entradas as $k => $entradas) {
			$data_evento = $entradas['data_evento'];

			$res_entradas[$k]['fraude'] = $this->verificaFraude($data_evento);
			$res_entradas[$k]['total_sem_consumo'] = $this->contaCartoesSemConsumoData($data_evento);
			$res_entradas[$k]['total_cartoes_consumo_obrigatorio'] = $this->contaCartoesConsumoObrigatorioData($data_evento);
		}
		return $res_entradas;
	}

	function contaEntradasDiasTotal($data_evento = false, $filtros = false) {
		if ($filtros['data_evento']) {
			$data_evento = $filtros['data_evento'];
		}
		if ($data_evento) {
			$where = " WHERE data_evento = '" . $data_evento . "' ";
		}
		$query = "SELECT  COUNT( DISTINCT(data_evento) )   as conta FROM rps_entradas $where  ";
		$res_entradas = $this->db->query($query);
		return $res_entradas[0]['conta'];
	}
	function verificaFraude($data_evento) {

		$query = "SELECT user_agent FROM rps_entradas WHERE data_evento = '" . $data_evento . "' GROUP BY user_agent LIMIT 2";
		$res_user_agent = $this->db->query($query);

		$query = "SELECT ip FROM rps_entradas WHERE data_evento = '" . $data_evento . "'  GROUP BY ip LIMIT 2";
		$res_ip = $this->db->query($query);


		$query = "SELECT id FROM rps_entradas WHERE data_evento = '" . $data_evento . "' AND TIME(data) >= '06:00' AND TIME(data) <= '23:00' LIMIT 2 ";
		$res_data = $this->db->query($query);

		if (!empty($res_ip) && count($res_ip) > 1) {
			return 1;
		}

		if (!empty($res_user_agent) && count($res_user_agent) > 1) {
			return 1;
		}

		if (!empty($res_data) && count($res_data) > 0) {
			return 1;
		}
		return 0;
	}
	function contaCartoesSemConsumoData($data) {
		$sql = "SELECT sum(entrou) as conta FROM rps_cartoes_sem_consumo WHERE rps_cartoes_sem_consumo.data_evento = '" . $data . "' AND rps_cartoes_sem_consumo.entrou = 1 GROUP BY rps_cartoes_sem_consumo.entrou";
		$res = $this->db->query($sql);
		return $res[0]['conta'];
	}
	function contaCartoesConsumoObrigatorioData($data) {
		$sql = "SELECT sum(entrou) as conta FROM rps_cartoes_consumo_obrigatorio WHERE rps_cartoes_consumo_obrigatorio.data_evento = '" . $data . "' AND rps_cartoes_consumo_obrigatorio.entrou = 1 GROUP BY rps_cartoes_consumo_obrigatorio.entrou";
		$res = $this->db->query($sql);
		return $res[0]['conta'];
	}

	function listaEntradasRPSDia($data, $id_chefe_equipa = false) {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
		$dbpagamentos = new pagamentos($this->db);
		if ($id_chefe_equipa) {
			$where = " AND  (rps.id = " . $id_chefe_equipa . " OR rps.id_chefe_equipa  = " . $id_chefe_equipa . ")  ";
		}

		$query = "SELECT sum(rps_entradas.quantidade) as total, rps.nome, rps.id, rps.comissao_guest FROM rps_entradas LEFT JOIN rps ON rps.id = rps_entradas.id_rp WHERE rps_entradas.data_evento = '" . $data . "' " . $where . "  GROUP BY rps_entradas.id_rp ORDER BY rps.nome ASC";
		$res_entradas = $this->db->query($query);
		if ($res_entradas) {
			foreach ($res_entradas as $res) {
				$res_final[$res['id']]['total'] = $res['total'];
				if ($res['comissao_guest'] == 1) {
					$res_final[$res['id']]['total_entradas']  = $dbpagamentos->converteEntradasToEuro($res_final[$res['id']]['total'], $res['id']) +  $dbpagamentos->converteEntradasBonusToEuro($res_final[$res['id']]['total'], $res['id']);
				}
				$res_final[$res['id']]['total_cartoes_consumo_obrigatorio'] = intval($res_final[$res['id']]['total_cartoes_consumo_obrigatorio']);
				$res_final[$res['id']]['total_sem_consumo'] = intval($res_final[$res['id']]['total_sem_consumo']);
				$res_final[$res['id']]['total_privados'] = intval($res_final[$res['id']]['total_privados']);
				$res_final[$res['id']]['nome'] = $res['nome'];
				$res_final[$res['id']]['id'] = $res['id'];
			}
		}

		$sql = "SELECT count(rps_cartoes_consumo_obrigatorio.id) as conta, rps.nome, rps.id FROM rps_cartoes_consumo_obrigatorio LEFT JOIN rps ON rps.id = rps_cartoes_consumo_obrigatorio.id_rp WHERE rps_cartoes_consumo_obrigatorio.data_evento = '" . $data . "' AND rps_cartoes_consumo_obrigatorio.entrou = 1  " . $where . "  GROUP BY rps_cartoes_consumo_obrigatorio.id_rp ORDER BY rps.nome ASC";
		$res_consumo_obrigatorio = $this->db->query($sql);
		if ($res_consumo_obrigatorio) {
			foreach ($res_consumo_obrigatorio as $res) {
				$res_final[$res['id']]['total_cartoes_consumo_obrigatorio'] = $res['conta'];
				$res_final[$res['id']]['nome'] = $res['nome'];
				$res_final[$res['id']]['id'] = $res['id'];
			}
		}

		$sql = "SELECT count(rps_cartoes_sem_consumo.id) as conta, rps.nome, rps.id FROM rps_cartoes_sem_consumo LEFT JOIN rps ON rps.id = rps_cartoes_sem_consumo.id_rp WHERE rps_cartoes_sem_consumo.data_evento = '" . $data . "' AND rps_cartoes_sem_consumo.entrou = 1  " . $where . "  GROUP BY rps_cartoes_sem_consumo.id_rp ORDER BY rps.nome ASC";
		$res_sem_consumo = $this->db->query($sql);
		if ($res_sem_consumo) {
			foreach ($res_sem_consumo as $res) {
				$res_final[$res['id']]['total_sem_consumo'] = $res['conta'];
				$res_final[$res['id']]['nome'] = $res['nome'];
				$res_final[$res['id']]['id'] = $res['id'];
			}
		}
		$query = "SELECT sum(venda_privados_garrafas.quantidade) as quantidade, SUM(venda_privados.total) as total, rps.nome, rps.id FROM venda_privados INNER JOIN venda_privados_garrafas ON venda_privados_garrafas.id_compra = venda_privados.id LEFT JOIN rps ON rps.id = venda_privados.id_rp INNER JOIN garrafas ON venda_privados_garrafas.id_garrafa = garrafas.id AND garrafas.comissao = 1  WHERE venda_privados.data_evento = '" . $data . "' AND venda_privados.total > 50  " . $where . "  GROUP BY venda_privados.id_rp DESC";
		$res_privados  = $this->db->query($query);

		if ($res_privados) {
			foreach ($res_privados as $res) {
				$res_final[$res['id']]['total_privados'] = $res['total'] * 0.05;
				$res_final[$res['id']]['nome'] = $res['nome'];
				$res_final[$res['id']]['id'] = $res['id'];
			}
		}

		$query = "SELECT sum(venda_garrafas_bar_garrafas.quantidade) as quantidade, SELECT sum(venda_garrafas_bar.total) as total, rps.nome, rps.id  FROM venda_garrafas_bar INNER JOIN venda_garrafas_bar_garrafas ON venda_garrafas_bar_garrafas.id_compra = venda_garrafas_bar.id LEFT JOIN rps ON rps.id = venda_garrafas_bar.id_rp INNER JOIN garrafas ON venda_garrafas_bar_garrafas.id_garrafa = garrafas.id AND garrafas.comissao = 1 WHERE venda_garrafas_bar.data_evento = '" . $data . "' AND venda_garrafas_bar.total > 50  " . $where . "   GROUP BY venda_garrafas_bar.id_rp DESC";
		$res_garrafas  = $this->db->query($query);

		if ($res_garrafas) {
			foreach ($res_garrafas as $res) {
				$res_final[$res['id']]['total_garrafas'] = $res['total'] * 0.05;
				$res_final[$res['id']]['nome'] = $res['nome'];
				$res_final[$res['id']]['id'] = $res['id'];
			}
		}

		return $res_final;
	}

	function devolveComissaoPrivados($id_rp, $data_evento) {

        $query = "SELECT count(f.data_evento) as quantidade, f.id_mesa as id_mesa, SUM(f.total) as total, f.data_evento FROM (SELECT venda_privados.id as id, count(venda_privados.data_evento) as quantidade, venda_privados.id_mesa as id_mesa, (venda_privados.total) as total, venda_privados.data_evento as data_evento FROM venda_privados INNER JOIN rps ON rps.id = venda_privados.id_rp INNER JOIN rps_cargos ON rps_cargos.id = rps.id_cargo INNER JOIN privados_salas_mesas_ocupacao ON venda_privados.id_mesa = privados_salas_mesas_ocupacao.id_mesa AND privados_salas_mesas_ocupacao.data_evento = venda_privados.data_evento  WHERE venda_privados.id_rp = " . $id_rp . " AND venda_privados.data_evento = '" . $data_evento . "' AND rps_cargos.privados_pagamentos = 1 GROUP BY venda_privados.data_evento, venda_privados.id_mesa ORDER BY venda_privados.id ASC) f  GROUP BY f.data_evento  ORDER BY f.id ASC";
        $resultado  = $this->db->query($query);

		if ($resultado) {
			return $resultado[0]['total'] * 0.05;
		}
	}
	function devolveComissaoGarrafas($id_rp, $data_evento) {
		$query = "SELECT sum(venda_garrafas_bar.total) as total FROM venda_garrafas_bar INNER JOIN venda_garrafas_bar_garrafas ON venda_garrafas_bar_garrafas.id_compra = venda_garrafas_bar.id WHERE venda_garrafas_bar.id_rp = " . $id_rp . " AND venda_garrafas_bar.data_evento = '" . $data_evento . "' GROUP BY venda_garrafas_bar.data_evento DESC";
		$resultado  = $this->db->query($query);
		return $resultado[0]['total'] * 0.05;
	}
	function devolveCartoesConsumoObrigatorio($pesquisa = false, $id_rp = false) {
		if (date('H') < 14) {
			$data = date('Y-m-d', strtotime('-1 day'));
			$where_entradas = " rps_cartoes_consumo_obrigatorio.data_evento = '" . $data . "'";
		} else {
			$data = date('Y-m-d');
			$where_entradas = " rps_cartoes_consumo_obrigatorio.data_evento = '" . $data . "'";
		}

		$where = "";
		if ($id_rp) {
			$where .= "  AND rps_cartoes_consumo_obrigatorio.id_rp = '" . $id_rp . "' ";
		}
		if ($pesquisa) {
			$where .= " AND rps_cartoes_consumo_obrigatorio.nome like '%" . $pesquisa . "%' ";
		}
		$sql = "SELECT  rps_cartoes_consumo_obrigatorio.*, rps.nome as nome_rp, rps.foto  FROM rps_cartoes_consumo_obrigatorio INNER JOIN rps ON rps.id = rps_cartoes_consumo_obrigatorio.id_rp  WHERE $where_entradas  $where ORDER BY rps_cartoes_consumo_obrigatorio.nome";
		$res = $this->db->query($sql);
		if ($res) {
			return $res;
		}
	}

	function RPSCartoesConsumoObrigatorioData() {
		if (date('H') < 14) {
			$data = date('Y-m-d', strtotime('-1 day'));
			$where_entradas = " rps_cartoes_consumo_obrigatorio.data_evento = '" . $data . "'";
		} else {
			$data = date('Y-m-d');
			$where_entradas = " rps_cartoes_consumo_obrigatorio.data_evento = '" . $data . "'";
		}

		$sql = "SELECT rps.id, rps.nome FROM rps_cartoes_consumo_obrigatorio INNER JOIN rps ON rps.id = rps_cartoes_consumo_obrigatorio.id_rp WHERE rps_cartoes_consumo_obrigatorio.data_evento = '" . $data . "' GROUP BY rps_cartoes_consumo_obrigatorio.id_rp  ORDER BY rps.nome ASC";
		$res = $this->db->query($sql);
		return $res;
	}


	function RPSCartoesSemConsumoData() {
		if (date('H') < 14) {
			$data = date('Y-m-d', strtotime('-1 day'));
			$where_entradas = " rps_cartoes_sem_consumo.data_evento = '" . $data . "'";
		} else {
			$data = date('Y-m-d');
			$where_entradas = " rps_cartoes_sem_consumo.data_evento = '" . $data . "'";
		}

		$sql = "SELECT rps.id, rps.nome FROM rps_cartoes_sem_consumo INNER JOIN rps ON rps.id = rps_cartoes_sem_consumo.id_rp WHERE rps_cartoes_sem_consumo.data_evento = '" . $data . "' GROUP BY rps_cartoes_sem_consumo.id_rp  ORDER BY rps.nome ASC";
		$res = $this->db->query($sql);
		return $res;
	}

	function devolveCartoesSemConsumo($pesquisa = false, $id_rp = false) {
		if (date('H') < 14) {
			$data = date('Y-m-d', strtotime('-1 day'));
			$where_entradas = " rps_cartoes_sem_consumo.data_evento = '" . $data . "'";
		} else {
			$data = date('Y-m-d');
			$where_entradas = " rps_cartoes_sem_consumo.data_evento = '" . $data . "'";
		}

		$where = "";
		if ($id_rp) {
			$where .= "  AND rps_cartoes_sem_consumo.id_rp = '" . $id_rp . "' ";
		}
		if ($pesquisa) {
			$where .= "  AND rps_cartoes_sem_consumo.nome like '%" . $pesquisa . "%' ";
		}

		$sql = "SELECT  rps_cartoes_sem_consumo.*, rps.nome as nome_rp, rps.foto  FROM rps_cartoes_sem_consumo INNER JOIN rps ON rps.id = rps_cartoes_sem_consumo.id_rp  WHERE $where_entradas $where ORDER BY rps_cartoes_sem_consumo.nome";
		$res = $this->db->query($sql);
		if ($res) {
			return $res;
		}
	}

	function listaCargosAtrasos() {

		$query = "SELECT rps_cargos.id FROM rps_cargos  WHERE rps_cargos.regras_atrasos = '1' ";
		$res_atrasos = $this->db->query($query);
		foreach ($res_atrasos as $k => $rs) {
			$res[$k] = $rs['id'];
		}
		return $res;
	}
	function verificaPresencaRP($id_rp) {

		if (date('H') < 14) {
			$data = date('Y-m-d', strtotime('-1 day'));
		} else {
			$data = date('Y-m-d');
		}

		$query = "SELECT count(*) as conta FROM presencas  WHERE presencas.id_rp = '" . $id_rp . "' AND presencas.data_evento= '" . $data . "'";
		$res_atrasos = $this->db->query($query);
		if ($res_atrasos[0]['conta'] > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function listaEntradasProdutoresDia($data) {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
		$dbpagamentos = new pagamentos($this->db);


		$query = "SELECT rps.nome, rps.id FROM rps WHERE id_cargo =  " . self::id_produtor;
		$res_entradas = $this->db->query($query);

		if ($res_entradas) {
			foreach ($res_entradas as $res_prod) {
				$res_final[$res_prod['id']]['nome'] = $res_prod['nome'];
				$res_final[$res_prod['id']]['id'] = $res_prod['id'];

				$query = "SELECT rps.nome, rps.id FROM rps WHERE id_produtor = " . $res_prod['id'];
				$res = $this->db->query($query);
				$ids_chefes = array_column($res, 'id');

				# ENTRADAS

				$query = "SELECT sum(rps_entradas.quantidade) as total FROM rps_entradas LEFT JOIN rps ON rps.id = rps_entradas.id_rp WHERE rps_entradas.data_evento = '" . $data . "' AND  (rps.id IN ('" . implode("', '", $ids_chefes) . "') OR rps.id_chefe_equipa IN ('" . implode("', '", $ids_chefes) . "') ) ";
				$res_entradas = $this->db->query($query);

				if ($res_entradas) {
					foreach ($res_entradas as $res) {
						$res_final[$res_prod['id']]['total'] = $res['total'];
					}
				}

				$query = "SELECT sum(venda_privados_garrafas.quantidade) as quantidade, SUM(venda_privados.total) as total FROM venda_privados INNER JOIN venda_privados_garrafas ON venda_privados_garrafas.id_compra = venda_privados.id LEFT JOIN rps ON rps.id = venda_privados.id_rp INNER JOIN garrafas ON venda_privados_garrafas.id_garrafa = garrafas.id AND garrafas.comissao = 1  WHERE venda_privados.data_evento = '" . $data . "'  AND  (rps.id IN ('" . implode("', '", $ids_chefes) . "') OR rps.id_chefe_equipa IN ('" . implode("', '", $ids_chefes) . "') )";
				$res_privados  = $this->db->query($query);

				if ($res_privados) {
					foreach ($res_privados as $res) {
						$res_final[$res_prod['id']]['total_privados'] = $res['total'];
					}
				}
				$query = "SELECT sum(venda_garrafas_bar.total) as total FROM venda_garrafas_bar INNER JOIN venda_garrafas_bar_garrafas ON venda_garrafas_bar_garrafas.id_compra = venda_garrafas_bar.id LEFT JOIN rps ON rps.id = venda_garrafas_bar.id_rp INNER JOIN garrafas ON venda_garrafas_bar_garrafas.id_garrafa = garrafas.id AND garrafas.comissao = 1 WHERE venda_garrafas_bar.data_evento = '" . $data . "'   AND  (rps.id IN ('" . implode("', '", $ids_chefes) . "') OR rps.id_chefe_equipa IN ('" . implode("', '", $ids_chefes) . "') )";
				$res_garrafas  = $this->db->query($query);

				if ($res_garrafas) {
					foreach ($res_garrafas as $res) {
						$res_final[$res_prod['id']]['total_garrafas'] = $res['total'];
					}
				}
			}
		}

		return $res_final;
	}

	function listaEntradasEquipasDia($data, $id_produtor) {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
		$dbpagamentos = new pagamentos($this->db);

		$query = "SELECT rps.nome, rps.id FROM rps WHERE id_produtor = " . $id_produtor;
		$res = $this->db->query($query);
		foreach ($res as $rs) {
			$res_final[$rs['id']]['nome'] = $rs['nome'];
			$res_final[$rs['id']]['id'] = $rs['id'];

			# ENTRADAS

			$query = "SELECT sum(rps_entradas.quantidade) as total FROM rps_entradas LEFT JOIN rps ON rps.id = rps_entradas.id_rp WHERE rps_entradas.data_evento = '" . $data . "' AND  (rps.id = " . $rs['id'] . " OR rps.id_chefe_equipa  = " . $rs['id'] . ")  ";

			$res_entradas = $this->db->query($query);

			if ($res_entradas) {
				foreach ($res_entradas as $res) {
					$res_final[$rs['id']]['total'] = $res['total'];
				}
			}

			$query = "SELECT sum(venda_privados_garrafas.quantidade) as quantidade, SUM(venda_privados.total) as total FROM venda_privados INNER JOIN venda_privados_garrafas ON venda_privados_garrafas.id_compra = venda_privados.id LEFT JOIN rps ON rps.id = venda_privados.id_rp INNER JOIN garrafas ON venda_privados_garrafas.id_garrafa = garrafas.id AND garrafas.comissao = 1  WHERE venda_privados.data_evento = '" . $data . "' AND  (rps.id = " . $rs['id'] . " OR rps.id_chefe_equipa  = " . $rs['id'] . ")  ";
			$res_privados  = $this->db->query($query);

			if ($res_privados) {
				foreach ($res_privados as $res) {
					$res_final[$rs['id']]['total_privados'] = $res['total'];
				}
			}
			$query = "SELECT sum(venda_garrafas_bar.total) as total FROM venda_garrafas_bar INNER JOIN venda_garrafas_bar_garrafas ON venda_garrafas_bar_garrafas.id_compra = venda_garrafas_bar.id LEFT JOIN rps ON rps.id = venda_garrafas_bar.id_rp INNER JOIN garrafas ON venda_garrafas_bar_garrafas.id_garrafa = garrafas.id AND garrafas.comissao = 1 WHERE venda_garrafas_bar.data_evento = '" . $data . "'   AND  (rps.id = " . $rs['id'] . " OR rps.id_chefe_equipa  = " . $rs['id'] . ") ";
			$res_garrafas  = $this->db->query($query);

			if ($res_garrafas) {
				foreach ($res_garrafas as $res) {
					$res_final[$rs['id']]['total_garrafas'] = $res['total'];
				}
			}
		}


		return $res_final;
	}
}
