<?php
class rp {
	const id_chefe_equipa = 20;
	const id_produtor = 21;

	function __construct($db, $id_rp) {
		$this->db = $db;
		$this->rp = $id_rp;
	}

	public function getIDChefeEquipa() {
		return self::id_chefe_equipa;
	}
	public function getIDProdutor() {
		return self::id_produtor;
	}
	function alterouPassword() {
		$query = "SELECT alterou_password FROM rps WHERE rps.id = '" . $this->rp . "'";
		$res = $this->db->query($query);
		return $res[0]['alterou_password'];
	}
	function permissao() {
		$query = "SELECT rps_cargos.cartao_sem_consumo, rps.permite_reservar_privados FROM rps INNER JOIN rps_cargos ON rps.id_cargo = rps_cargos.id WHERE rps.id = '" . $this->rp . "'";
		$res = $this->db->query($query);

		if ($res[0]['cartao_sem_consumo'] == 1 || $res[0]['permite_reservar_privados'] == 1) {
			return 1;
		}
	}
	function cargo() {
		$query = "SELECT rps_cargos.id FROM rps INNER JOIN rps_cargos ON rps.id_cargo = rps_cargos.id WHERE rps.id = '" . $this->rp . "'";
		$res = $this->db->query($query);
		if ($res) {
			return $res[0]['id'];
		}
	}
	function permissaoPrivados() {
		$where = " AND rps.id_cargo in (1,5)";
		$query = "SELECT rps.* FROM rps WHERE rps.id = '" . $this->rp . "' $where ORDER BY nome";
		$res = $this->db->query($query);

		if ($res) {
			return 1;
		}
	}
	function permissaoDisponibilidadeMesas() {
		$query = "SELECT rps.disponibilidade_mesas, rps.permite_reservar_privados FROM rps WHERE rps.id = '" . $this->rp . "' ORDER BY nome";
		$res = $this->db->query($query);

		if ($res[0]['disponibilidade_mesas'] == 1 || $res[0]['permite_reservar_privados'] == 1) {
			return 1;
		}
	}

	function devolveCargo() {

		$query = "SELECT rps.id_cargo FROM rps WHERE rps.id = '" . $this->rp . "'";
		$res = $this->db->query($query);

		if ($res) {
			return $res[0]['id_cargo'];
		}
	}
	function devolvePassword() {
		$query = "SELECT password FROM rps WHERE rps.id = '" . $this->rp . "'";
		$res = $this->db->query($query);
		return $res[0]['password'];
	}

	function listaRps() {
		$query = "SELECT * FROM rps ORDER BY rps.nome ASC";
		$res = $this->db->query($query);
		return $res;
	}


	function devolveNomeRp($id) {
		$query = "SELECT * FROM rps WHERE id = " . $id . " ORDER BY rps.nome ASC";
		$res = $this->db->query($query);
		return $res[0]['nome'];
	}

	function devolveInfo() {
		$sql = "SELECT rps.foto, rps.nome, rps.id, rps_cargos.nome as nome_cargo, rps.qrcode FROM rps LEFT JOIN rps_cargos ON rps.id_cargo = rps_cargos.id WHERE rps.id = " . $this->rp . " ";
		$res = $this->db->query($sql);
		if ($res) {
			if ($res[0]['foto'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/rps/" . $res[0]['foto'])) {
				$res[0]['foto'] = "/fotos/rps/" . $res[0]['foto'];
			} else {
				unset($res[0]['foto']);
			}
			return $res[0];
		}
	}
	#cartões consumo obrigatorio
	function validaCartaoObrigatorio($data, $id = 0) {
		if ($id > 0) {
			$where = " AND rps_cartoes_consumo_obrigatorio.id != " . $id . " ";
		}
		$sql = "SELECT count(id) as conta FROM rps_cartoes_consumo_obrigatorio WHERE rps_cartoes_consumo_obrigatorio.id_rp = " . $this->rp . " $where AND rps_cartoes_consumo_obrigatorio.data_evento = '" . $data . "' ";
		$res = $this->db->query($sql);
		return $res[0]['conta'];
	}
	function devolveCartoesConsumoObrigatorio($entrou = false, $data_evento = false) {

		$query = "";
		if ($entrou) {
			$query .= " AND rps_cartoes_consumo_obrigatorio.entrou = 1 ";
		}
		if ($data_evento) {
			$query .= " AND rps_cartoes_consumo_obrigatorio.data_evento = '" . $data_evento . "'";
		}

		$sql = "SELECT * FROM rps_cartoes_consumo_obrigatorio WHERE rps_cartoes_consumo_obrigatorio.id_rp = " . $this->rp . " $query ORDER BY data_evento DESC";
		$res = $this->db->query($sql);
		if ($res) {
			return $res;
		}
	}
	function apagaCartaoConsumoObrigatorio($id) {

		$sql = "DELETE FROM rps_cartoes_consumo_obrigatorio WHERE rps_cartoes_consumo_obrigatorio.id = " . $id . " AND rps_cartoes_consumo_obrigatorio.id_rp = " . $this->rp;
		$res = $this->db->query($sql);
		if ($res) {
			return $res;
		}
	}
	function devolveCartaoConsumoObrigatorio($id) {

		$sql = "SELECT * FROM rps_cartoes_consumo_obrigatorio WHERE rps_cartoes_consumo_obrigatorio.id = " . $id . " AND rps_cartoes_consumo_obrigatorio.id_rp = " . $this->rp;
		$res = $this->db->query($sql);
		if ($res) {
			return $res[0];
		}
	}

	#cartões sem consumo obrigatorio
	function validaCartaoSemConsumo($data, $id = 0) {
		if ($id > 0) {
			$where = " AND rps_cartoes_sem_consumo.id != " . $id . " ";
		}
		$sql = "SELECT count(id) as conta FROM rps_cartoes_sem_consumo WHERE rps_cartoes_sem_consumo.id_rp = " . $this->rp . " $where AND rps_cartoes_sem_consumo.data_evento = '" . $data . "' ";
		$res = $this->db->query($sql);
		return $res[0]['conta'];
	}
	function devolveCartoesSemConsumo($entrou = false, $data_evento = false) {
		$query = "";
		if ($entrou) {
			$query .= " AND rps_cartoes_sem_consumo.entrou = 1 ";
		}
		if ($data_evento) {
			$query .= " AND rps_cartoes_sem_consumo.data_evento = '" . $data_evento . "'";
		}

		$sql = "SELECT * FROM rps_cartoes_sem_consumo WHERE rps_cartoes_sem_consumo.id_rp = " . $this->rp . " $query ORDER BY data_evento DESC";
		$res = $this->db->query($sql);
		if ($res) {
			return $res;
		}
	}
	function apagaCartaoSemConsumo($id) {

		$sql = "DELETE FROM rps_cartoes_sem_consumo WHERE rps_cartoes_sem_consumo.id = " . $id . " AND rps_cartoes_sem_consumo.id_rp = " . $this->rp;
		$res = $this->db->query($sql);
		if ($res) {
			return $res;
		}
	}
	function devolveCartaoSemConsumo($id) {

		$sql = "SELECT * FROM rps_cartoes_sem_consumo WHERE rps_cartoes_sem_consumo.id = " . $id . " AND rps_cartoes_sem_consumo.id_rp = " . $this->rp;
		$res = $this->db->query($sql);
		if ($res) {
			return $res[0];
		}
	}

	function listaEventosRP() {

		require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
		$dbpagamentos = new pagamentos($this->db);

		if (date('H') < 14) {
			$data_actual = date('Y-m-d', strtotime('-1 day'));
		} else {
			$data_actual = date('Y-m-d');
		}
		$query = "SELECT data_evento FROM rps_entradas  GROUP BY data_evento DESC";
		$eventos = $this->db->query($query);

		if ($eventos) {
			foreach ($eventos as $k => $evento) {
				$query = "SELECT sum(quantidade) as quantidade FROM rps_entradas  WHERE  id_rp = " . $this->rp . " AND data_evento = '" . $evento['data_evento'] . "' GROUP BY data_evento DESC";
				$eventoRP = $this->db->query($query);
				if ($evento['data_evento'] == $data_actual) {
					$eventos_return[$k]['estado'] = "A decorrer";
				} else {
					$eventos_return[$k]['estado'] = "Passado";
				}
				$eventos_return[$k]['data_evento'] = date('d/m/Y', strtotime($evento['data_evento']));
				$eventos_return[$k]['quantidade'] = intval($eventoRP[0]['quantidade']);
				$eventos_return[$k]['cartoes_sem_consumo'] = intval(count((array) $this->devolveCartoesSemConsumo(1, $evento['data_evento'])));
				$eventos_return[$k]['cartoes_consumo_obrigatorio'] = intval(count((array) $this->devolveCartoesConsumoObrigatorio(1, $evento['data_evento'])));

				$eventos_return[$k]['comissao_entradas'] = $dbpagamentos->converteEntradasToEuro($eventos_return[$k]['quantidade'], $this->rp);
				$eventos_return[$k]['comissao_bonus_entradas'] = $dbpagamentos->converteEntradasBonusToEuro($eventos_return[$k]['quantidade'], $this->rp);

				$comissoes_equipa = $dbpagamentos->listaEventosEquipaRP($this->rp, $evento['data_evento']);

				$eventos_return[$k]['comissao_equipa_entradas'] = $comissoes_equipa['comissao'];
				$eventos_return[$k]['comissao_equipa_bonus_entradas'] =  $comissoes_equipa['comissao_bonus'];

				$eventos_return[$k]['comissao_privados'] = $this->devolveComissaoPrivados($evento['data_evento']);
				$eventos_return[$k]['comissao_garrafas'] = $this->devolveComissaoGarrafas($evento['data_evento']);
			}
		}
		return $eventos_return;
	}
	function listaEventosProdutores() {

		require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
		$dbpagamentos = new pagamentos($this->db);

		if (date('H') < 14) {
			$data_actual = date('Y-m-d', strtotime('-1 day'));
		} else {
			$data_actual = date('Y-m-d');
		}
		$query = "SELECT data_evento FROM rps_entradas  GROUP BY data_evento DESC";
		$eventos = $this->db->query($query);

		if ($eventos) {
			foreach ($eventos as $k => $evento) {
				if ($evento['data_evento'] == $data_actual) {
					$eventos_return[$k]['estado'] = "A decorrer";
				} else {
					$eventos_return[$k]['estado'] = "Passado";
				}
				$eventos_return[$k]['data_evento'] = date('d/m/Y', strtotime($evento['data_evento']));
				$eventos_return[$k]['data_evento_sql'] = $evento['data_evento'];

				#saber stats equipa
				$query = "SELECT id FROM rps WHERE id_produtor = " . $this->rp;
				$res_equipa = $this->db->query($query);
				$ids_chefes_equipa = array_column($res_equipa, 'id');

				#get rps e chefes de equipa
				$query = "SELECT sum(rps_entradas.quantidade) as total FROM `rps_entradas` INNER JOIN rps ON rps.id = rps_entradas.id_rp WHERE (rps.id IN ('" . implode("', '", $ids_chefes_equipa) . "') OR  rps.id_chefe_equipa IN ('" . implode("', '", $ids_chefes_equipa) . "')) AND rps_entradas.data_evento = '" . $evento['data_evento'] . "'";
				$res_entradas = $this->db->query($query);

				$eventos_return[$k]['entradas'] = $res_entradas[0]['total'];
				$eventos_return[$k]['numero_privados'] = $this->devolveNumeroPrivadosequipa($evento['data_evento'], $ids_chefes_equipa);
				$eventos_return[$k]['total_vendas_privados'] = $this->devolveTotalVendasPrivadosEquipa($evento['data_evento'], $ids_chefes_equipa);
				$eventos_return[$k]['comissao_privados'] = $eventos_return[$k]['total_vendas_privados'] * 0.05;
				$eventos_return[$k]['melhor_equipa_entradas'] = $this->devolveMelhorEquipaEntradas($evento['data_evento'], $ids_chefes_equipa);
				$eventos_return[$k]['melhor_equipa_rp_entradas'] = $this->devolveMelhorRPEquipasEntradas($evento['data_evento'], $ids_chefes_equipa);
				if ((int) $eventos_return[$k]['entradas'] == 0 &&  (int) $eventos_return[$k]['numero_privados'] == 0) {
					unset($eventos_return[$k]);
				}
			}
		}

		return $eventos_return;
	}
	function listaEventosEquipa() {

		require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
		$dbpagamentos = new pagamentos($this->db);

		if (date('H') < 14) {
			$data_actual = date('Y-m-d', strtotime('-1 day'));
		} else {
			$data_actual = date('Y-m-d');
		}
		$query = "SELECT data_evento FROM rps_entradas  GROUP BY data_evento DESC";
		$eventos = $this->db->query($query);

		if ($eventos) {
			foreach ($eventos as $k => $evento) {
				if ($evento['data_evento'] == $data_actual) {
					$eventos_return[$k]['estado'] = "A decorrer";
				} else {
					$eventos_return[$k]['estado'] = "Passado";
				}
				$eventos_return[$k]['data_evento'] = date('d/m/Y', strtotime($evento['data_evento']));
				$eventos_return[$k]['data_evento_sql'] = $evento['data_evento'];


				$eventosRPEquipa = $dbpagamentos->listaEventosEquipaRP( $this->rp, $eventos_return[$k]['data_evento_sql']);
				if ($eventosRPEquipa) {
					$eventos_return[$k]['entradas'] = $eventosRPEquipa['entrou'];
					$eventos_return[$k]['entradas_equipa_comissao'] = $eventosRPEquipa['comissao'];
					$eventos_return[$k]['entradas_equipa_comissao_bonus'] = $eventosRPEquipa['comissao_bonus'];
				}

				$eventos_return[$k]['numero_privados'] = $this->devolveNumeroPrivadosequipa($eventos_return[$k]['data_evento_sql'], array( $this->rp));
				$eventos_return[$k]['total_vendas_privados'] = $this->devolveTotalVendasPrivadosEquipa($eventos_return[$k]['data_evento_sql'], array( $this->rp));
				$eventos_return[$k]['comissao_privados'] = $eventos_return[$k]['total_vendas_privados'] * 0.05;
				$eventos_return[$k]['melhor_equipa_rp_entradas'] = $this->devolveMelhorRPEquipasEntradas($eventos_return[$k]['data_evento_sql'], array( $this->rp));
				if((int) $eventos_return[$k]['entradas'] == 0 && (int) $eventos_return[$k]['numero_privados'] == 0){
					unset($eventos_return[$k]);
				}
			}
		}

		return $eventos_return;
	}
	function listaEstatisticasEquipasProdutor($data_evento) {

		require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
		$dbpagamentos = new pagamentos($this->db);

		#saber stats equipa
		$query = "SELECT id, nome, foto FROM rps WHERE id_produtor = " . $this->rp;
		$res_equipa = $this->db->query($query);

		foreach ($res_equipa as $k => $res) {
			$res_final[$k]['rp']['nome'] = $res['nome'];
			$res_final[$k]['rp']['foto'] = $res['foto'];
			$res_final[$k]['rp']['id'] = $res['id'];



			$eventosRPEquipa = $dbpagamentos->listaEventosEquipaRP($res['id'], $data_evento);
			if ($eventosRPEquipa) {
				$res_final[$k]['entradas'] = $eventosRPEquipa['entrou'];
				$res_final[$k]['entradas_equipa_comissao'] = $eventosRPEquipa['comissao'];
				$res_final[$k]['entradas_equipa_comissao_bonus'] = $eventosRPEquipa['comissao_bonus'];
			}

			$res_final[$k]['numero_privados'] = $this->devolveNumeroPrivadosequipa($data_evento, array($res['id']));
			$res_final[$k]['total_vendas_privados'] = $this->devolveTotalVendasPrivadosEquipa($data_evento, array($res['id']));
			$res_final[$k]['comissao_privados'] = $res_final[$k]['total_vendas_privados'] * 0.05;
			$res_final[$k]['melhor_equipa_rp_entradas'] = $this->devolveMelhorRPEquipasEntradas($data_evento, array($res['id']));
		}
		return $res_final;
	}
	function listaEstatisticasStaffEquipa($data_evento, $equipa) {

		require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
		$dbpagamentos = new pagamentos($this->db);


		#saber stats equipa
		$query = "SELECT id, nome, foto FROM rps WHERE (id_chefe_equipa = " . $equipa . " OR id = " . $equipa . " ) ORDER BY id_chefe_equipa ASC";
		$res_equipa = $this->db->query($query);

		foreach ($res_equipa as $k => $res) {
			$res_final[$k]['rp']['nome'] = $res['nome'];
			$res_final[$k]['rp']['foto'] = $res['foto'];
			$res_final[$k]['rp']['id'] = $res['id'];

			#get rps e chefes de equipa
			$query = "SELECT sum(rps_entradas.quantidade) as total FROM `rps_entradas` INNER JOIN rps ON rps.id = rps_entradas.id_rp WHERE rps.id  = '" . $res['id'] . "' AND rps_entradas.data_evento = '" . $data_evento . "'";
			$res_entradas = $this->db->query($query);

			$res_final[$k]['entradas'] = $res_entradas[0]['total'];

			$eventosRP = $dbpagamentos->listaEventosRP($res['id'], $data_evento);
			$res_final[$k]['entradas_comissao'] = $eventosRP['comissao'];
			$res_final[$k]['entradas_comissao_bonus'] = $eventosRP['comissao_bonus'];


			$eventosRPEquipa = $dbpagamentos->listaEventosEquipaRP($res['id'], $data_evento);
			if ($eventosRPEquipa) {
				$res_final[$k]['entradas_equipa'] = $eventosRPEquipa['entrou'];
				$res_final[$k]['entradas_equipa_comissao'] = $eventosRPEquipa['comissao'];
				$res_final[$k]['entradas_equipa_comissao_bonus'] = $eventosRPEquipa['comissao_bonus'];
			}


			$privadosComissao = $dbpagamentos->devolveComissaoPrivados($res['id'], $data_evento);
			$res_final[$k]['comissao_privados'] = $privadosComissao['comissao'];
		}
		return $res_final;
	}
	function devolveMelhorEquipaEntradas($data_evento, $ids = array()) {

		#get rps e chefes de equipa
		$query = "SELECT sum(rps_entradas.quantidade) as total, (IF(rps.id_chefe_equipa, rps.id_chefe_equipa, rps.id)) as id_virtual FROM `rps_entradas` INNER JOIN rps ON rps.id = rps_entradas.id_rp WHERE (rps.id IN ('" . implode("', '", $ids) . "') OR  rps.id_chefe_equipa IN ('" . implode("', '", $ids) . "')) AND rps_entradas.data_evento = '" . $data_evento . "' GROUP BY id_virtual ORDER BY total DESC LIMIT 1 ";
		$res_entradas = $this->db->query($query);
		if ($res_entradas[0]) {
			return $this->devolveNomeRp($res_entradas[0]['id_virtual']);
		}
	}
	function devolveMelhorRPEquipasEntradas($data_evento, $ids = array()) {

		#get rps e chefes de equipa
		$query = "SELECT rps_entradas.quantidade, rps.nome, rps.id FROM `rps_entradas` INNER JOIN rps ON rps.id = rps_entradas.id_rp WHERE (rps.id IN ('" . implode("', '", $ids) . "') OR  rps.id_chefe_equipa IN ('" . implode("', '", $ids) . "')) AND rps_entradas.data_evento = '" . $data_evento . "'  ORDER BY quantidade DESC  ";
		$res_entradas = $this->db->query($query);

		if ($res_entradas[0]) {
			return $res_entradas[0]['nome'];
		}
	}


	function devolveTotalVendasPrivadosEquipa($data_evento, $ids = array()) {

		$query = "SELECT SUM(venda_privados.total) as total FROM venda_privados INNER JOIN rps ON venda_privados.id_rp = rps.id  WHERE (rps.id IN ('" . implode("', '", $ids) . "') OR  rps.id_chefe_equipa IN ('" . implode("', '", $ids) . "')) AND venda_privados.data_evento = '" . $data_evento . "' AND venda_privados.total > 50 GROUP BY venda_privados.data_evento";
		$resultado2  = $this->db->query($query);
		if ($resultado2) {
			return $resultado2[0]['total'];
		}
	}
	function devolveNumeroPrivadosEquipa($data_evento, $ids = array()) {

		$query = "SELECT count(venda_privados.id) as total FROM venda_privados INNER JOIN rps ON venda_privados.id_rp = rps.id  WHERE (rps.id IN ('" . implode("', '", $ids) . "') OR  rps.id_chefe_equipa IN ('" . implode("', '", $ids) . "')) AND venda_privados.data_evento = '" . $data_evento . "' AND venda_privados.total > 50 GROUP BY venda_privados.data_evento";

		$resultado2  = $this->db->query($query);

		if ($resultado2) {
			return $resultado2[0]['total'];
		}
	}
	function devolveComissaoPrivados($data_evento) {

        $query = "SELECT count(f.data_evento) as quantidade, f.id_mesa as id_mesa, SUM(f.total) as total, f.data_evento FROM (SELECT venda_privados.id as id, count(venda_privados.data_evento) as quantidade, venda_privados.id_mesa as id_mesa, (venda_privados.total) as total, venda_privados.data_evento as data_evento FROM venda_privados INNER JOIN rps ON rps.id = venda_privados.id_rp INNER JOIN rps_cargos ON rps_cargos.id = rps.id_cargo INNER JOIN privados_salas_mesas_ocupacao ON venda_privados.id_mesa = privados_salas_mesas_ocupacao.id_mesa AND privados_salas_mesas_ocupacao.data_evento = venda_privados.data_evento  WHERE venda_privados.id_rp = " . $this->rp . " AND venda_privados.data_evento = '" . $data_evento . "' AND rps_cargos.privados_pagamentos = 1 GROUP BY venda_privados.data_evento, venda_privados.id_mesa ORDER BY venda_privados.id ASC) f  GROUP BY f.data_evento  ORDER BY f.id ASC";
        $resultado  = $this->db->query($query);

		if ($resultado) {
			return $resultado[0]['total'] * 0.05;
		}
	}
	function devolveComissaoGarrafas($data_evento) {
		$query = "SELECT sum(venda_garrafas_bar.total) as total FROM venda_garrafas_bar INNER JOIN venda_garrafas_bar_garrafas ON venda_garrafas_bar_garrafas.id_compra = venda_garrafas_bar.id  INNER JOIN garrafas ON venda_garrafas_bar_garrafas.id_garrafa = garrafas.id AND garrafas.comissao = 1 WHERE venda_garrafas_bar.id_rp = " . $this->rp . " AND venda_garrafas_bar.data_evento = '" . $data_evento . "' AND venda_garrafas_bar.total > 50 GROUP BY venda_garrafas_bar.data_evento DESC";
		$resultado  = $this->db->query($query);

		return $resultado[0]['total'] * 0.05;
	}
	#convites
	function devolveConvites($data_evento = false) {

		$query = "";
		if ($data_evento) {
			$query .= " AND convites.data_evento = '" . $data_evento . "'";
		}

		$sql = "SELECT * FROM convites WHERE convites.id_rp = " . $this->rp . " $query ORDER BY data_evento DESC";
		$res = $this->db->query($sql);
		if ($res) {
			return $res;
		}
	}
	function apagaConvite($id) {

		$sql = "DELETE FROM convites WHERE convites.id = " . $id . " AND convites.id_rp = " . $this->rp;
		$res = $this->db->query($sql);
		if ($res) {
			return $res;
		}
	}
	function devolveConvite($id) {

		$sql = "SELECT * FROM convites WHERE convites.id = " . $id . " AND convites.id_rp = " . $this->rp;
		$res = $this->db->query($sql);
		if ($res) {
			return $res[0];
		}
	}
	function verificaMD5($md5) {
		$sql = "SELECT count(*) as conta FROM convites WHERE convites.md5 = '" . $md5 . "'";
		$res = $this->db->query($sql);

		if ($res[0]['conta']) {
			return $res[0]['conta'];
		}
	}
	function devolvePagamentos() {

		$sql = "SELECT * FROM conta_corrente WHERE id_rp  = '" . $this->rp . "' ORDER BY data DESC";
		$res = $this->db->query($sql);
		foreach ($res as $k => $rs) {
			$res[$k]['linhas'] = $this->devolveLinhasPagamento($rs['id']);
		}
		return $res;
	}
	function devolveLinhasPagamento($id_conta_corrente) {
		$query = "SELECT nome, descricao, valor FROM conta_corrente_linhas WHERE id_conta_corrente = '" . $id_conta_corrente . "'  ORDER BY id ASC";
		$res = $this->db->query($query);
		return $res;
	}

	/** ACCOUNT TYPES */
}
