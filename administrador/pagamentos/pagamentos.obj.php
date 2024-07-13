<?php
class pagamentos
{
    function __construct($db)
    {
        $this->db = $db;
    }
    function listaLogicaAtrasos()
    {

        $query = "SELECT * FROM logica_atrasos ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res;
    }

    function devolveLogicaAtrasos($id)
    {
        $query = "SELECT * FROM logica_atrasos WHERE id = " . $id . "   ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res[0];
    }

    function listaLogicaPagamentos()
    {

        $query = "SELECT * FROM logica_pagamentos ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res;
    }

    function devolveLogicaPagamentos($id)
    {
        $query = "SELECT * FROM logica_pagamentos WHERE id = " . $id . "   ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res[0];
    }
    function converteEntradasToEuro($entradas, $id_rp, $entrou = 0)
    {
        if ($id_rp) {

            $query = "SELECT rps_cargos.regra_entradas, rps.id, rps.nome FROM rps INNER JOIN rps_cargos ON rps.id_cargo = rps_cargos.id WHERE rps.id = '" . $id_rp . "' LIMIT 1";
            $resentradas = $this->db->query($query);
            if ($resentradas[0]['regra_entradas'] == 1) {

                $query = "SELECT * FROM logica_pagamentos WHERE pessoal = 1 AND sessao = " . $entrou . " ORDER BY de ASC";
                $res = $this->db->query($query);

                $total = 0;
                $fim = 0;
                $sobra = 0;
                foreach ($res as $k => $rs) {
                    if ($fim == 0) {
                        $ate = $rs['ate'];
                        $de = $rs['de'];
                        $valor = $rs['valor'];
                        $numero_elementos = $ate - $de;
                        if ($k == 0) {
                            $sobra = $entradas - $numero_elementos;
                        } else {
                            $sobra = $sobra - $numero_elementos;
                        }
                        if ($sobra <= 0) {
                            $fim = 1;

                            $total += ($sobra + $numero_elementos) * $valor;
                        } else {
                            $total += $numero_elementos * $valor;
                        }
                    }
                }
            } else {
                $total = 0;
            }

            return $total;
        }
    }
    function converteEntradasBonusToEuro($entradas, $id_rp)
    {
        if ($id_rp) {

            $query = "SELECT rps_cargos.regra_entradas, rps.id, rps.nome FROM rps INNER JOIN rps_cargos ON rps.id_cargo = rps_cargos.id WHERE rps.id = '" . $id_rp . "' LIMIT 1";
            $resentradas = $this->db->query($query);
            if ($resentradas[0]['regra_entradas'] == 1) {
                $query = "SELECT * FROM logica_bonus WHERE pessoal = 1 AND numero_pessoas <= '$entradas'  ORDER BY numero_pessoas DESC LIMIT 1";
                $res = $this->db->query($query);
                $total = $res[0]['bonus'];
            } else {
                $total = 0;
            }

            return $total;
        }
    }
    function converteEntradasEquipaToEuro($entradas, $id_rp)
    {
        if ($id_rp) {

            $query = "SELECT rps_cargos.regra_entradas, rps.id, rps.nome FROM rps INNER JOIN rps_cargos ON rps.id_cargo = rps_cargos.id WHERE rps.id = '" . $id_rp . "' LIMIT 1";
            $resentradas = $this->db->query($query);
            if ($resentradas[0]['regra_entradas'] == 1) {

                $query = "SELECT * FROM logica_pagamentos WHERE equipa = 1  ORDER BY de ASC";
                $res = $this->db->query($query);
                $total = 0;
                $fim = 0;
                $sobra = 0;
                foreach ($res as $k => $rs) {
                    if ($fim == 0) {
                        $ate = $rs['ate'];
                        $de = $rs['de'];
                        $valor = $rs['valor'];
                        $numero_elementos = $ate - $de;
                        if ($k == 0) {
                            $sobra = $entradas - $numero_elementos;
                        } else {
                            $sobra = $sobra - $numero_elementos;
                        }
                        if ($sobra <= 0) {
                            $fim = 1;

                            $total += ($sobra + $numero_elementos) * $valor;
                        } else {
                            $total += $numero_elementos * $valor;
                        }
                    }
                }
            } else {
                $total = 0;
            }

            return $total;
        }
    }
    function converteEntradasBonusEquipaToEuro($entradas, $id_rp)
    {
        if ($id_rp) {

            $query = "SELECT rps_cargos.regra_entradas, rps.id, rps.nome FROM rps INNER JOIN rps_cargos ON rps.id_cargo = rps_cargos.id WHERE rps.id = '" . $id_rp . "' LIMIT 1";
            $resentradas = $this->db->query($query);
            if ($resentradas[0]['regra_entradas'] == 1) {
                $query = "SELECT * FROM logica_bonus WHERE equipa = 1 AND numero_pessoas <= '$entradas'  ORDER BY numero_pessoas DESC LIMIT 1";
                $res = $this->db->query($query);
                $total = $res[0]['bonus'];
            } else {
                $total = 0;
            }

            return $total;
        }
    }

    function listaEventosRP($id_rp, $data_evento)
    {

        #saber se deu entrada ou não
        $query = "SELECT * FROM presencas WHERE id_rp = '" . $id_rp . "' AND data_evento = '" . $data_evento . "'";
        $entradaRP = $this->db->query($query);

        $entrouNoite = 0;
        $extraDescricao = "(SEM SESSÃO)";
        if($entradaRP) {
            $entrouNoite = 1;
            $extraDescricao = "(COM SESSÃO)";
        }

        $query = "SELECT sum(rps_entradas.quantidade) as quantidade FROM rps_entradas INNER JOIN rps ON rps.id = rps_entradas.id_rp AND rps.comissao_guest = 1  WHERE  rps_entradas.id_rp = " . $id_rp . " AND rps_entradas.data_evento = '" . $data_evento . "' GROUP BY rps_entradas.data_evento DESC";
        $eventoRP = $this->db->query($query);


        if ($eventoRP[0]['quantidade'] > 0) {
            $eventos_return['entrou'] = intval($eventoRP[0]['quantidade']);
            $eventos_return['descricao'] = "<b>" . $data_evento . " " . $extraDescricao . "</b>: " . intval($eventoRP[0]['quantidade']) . " entradas";
            $eventos_return['comissao'] = $this->converteEntradasToEuro($eventoRP[0]['quantidade'], $id_rp, $entrouNoite);
            $eventos_return['descricao_bonus'] = "<b>" . $data_evento . "</b>: Bonus Entradas: ";
            $eventos_return['comissao_bonus'] = $this->converteEntradasBonusToEuro($eventoRP[0]['quantidade'], $id_rp);

            return $eventos_return;
        }
    }

    function listaEventosEquipaRP($id_rp, $data_evento)
    {
        $query = " SELECT COUNT(id) as conta FROM rps WHERE id_chefe_equipa = '" . $id_rp . "'";
        $elementos = $this->db->query($query);


        $query = "SELECT sum(rps_entradas.quantidade) as quantidade FROM rps_entradas INNER JOIN rps ON rps.id = rps_entradas.id_rp AND rps.comissao_guest = 1  WHERE ( rps.id_chefe_equipa = " . $id_rp . " ) AND rps_entradas.data_evento = '" . $data_evento . "' GROUP BY rps_entradas.data_evento DESC";
        $eventoRP = $this->db->query($query);

        if ($eventoRP[0]['quantidade'] > 0) {
            $eventos_return['entrou'] = intval($eventoRP[0]['quantidade']);
            $eventos_return['descricao'] = "<b>" . $data_evento . "</b> Equipa ( " . $elementos[0]["conta"] . " elementos ): " . intval($eventoRP[0]['quantidade']) . " entradas";
            $eventos_return['comissao'] = ($eventoRP[0]['quantidade'] >= 25) ? ($this->converteEntradasEquipaToEuro($eventoRP[0]['quantidade'], $id_rp)) : 0;
            $eventos_return['descricao_bonus'] = "<b>" . $data_evento . "</b>: Bónus de Equipa: ";
            $eventos_return['comissao_bonus'] = $this->converteEntradasBonusEquipaToEuro($eventoRP[0]['quantidade'], $id_rp);

            return $eventos_return;
        }
    }
    function devolveDatasParaPagamento($id_rp)
    {
        $where = "";

        $query = "SELECT data_minima_pagamentos FROM rps WHERE id = '" . $id_rp . "'";
        $min = $this->db->query($query);

        if ($min[0]['data_minima_pagamentos'] > "0000-00-00") {
            $where .= " AND data_evento >= '" . $min[0]['data_minima_pagamentos'] . "'";
        }

        $query = "SELECT data_evento FROM conta_corrente_eventos WHERE id_rp = '" . $id_rp . "' GROUP BY data_evento DESC";
        $datas_pagas = $this->db->query($query);
        if ($datas_pagas) {
            foreach ($datas_pagas as $data) {
                $datas_ja_pagas[] = $data['data_evento'];
            }
            $where .= " AND data_evento NOT IN('" . implode("', '", $datas_ja_pagas) . "') ";
        }
        $query = "SELECT data_evento FROM rps_entradas WHERE 1=1 $where GROUP BY data_evento DESC";
        $res_entradas = $this->db->query($query);
        return $res_entradas;
    }
    function devolvePagamento($id_rp)
    {
        if ($id_rp > 0) {
            $divida = $this->devolveDividaRP($id_rp);

            $garrafas = $this->devolveComissaoGarrafas($id_rp);

            $datas = $this->devolveDatasParaPagamento($id_rp);

            $return = array();
            if ($datas) {

                foreach ($datas as $data) {
                    $data_evento = $data['data_evento'];

                    $guest = $this->listaEventosRP($id_rp, $data_evento);
                    $guest_team = $this->listaEventosEquipaRP($id_rp, $data_evento);

                    $privados = $this->devolveComissaoPrivados($id_rp, $data_evento);
                    $privados_chefe = $this->devolveComissaoPrivadosChefe($id_rp, $data_evento);
                    $atrasos = $this->devolveAtrasos($id_rp, $data_evento);
                    // $convites = $this->devolveValidaConvite($id_rp, $data_evento);
                    $sessoes = $this->devolveComissaoSessoesChefe($id_rp, $data_evento, $guest_team["entrou"]);

                    if ($guest['comissao'] > 0) {
                        $return['guest']['comissao'] += $guest["comissao"];
                        $descricao_guest = $guest["descricao"] . " - " . euro($guest["comissao"]);
                        $return['guest']["descricao"] = $return['guest']['descricao'] ? $return['guest']['descricao'] . " </br> " . $descricao_guest : "" . $descricao_guest;
                    }
                    if ($guest['comissao_bonus'] > 0) {
                        $return['guest']['comissao_bonus'] += $guest["comissao_bonus"];
                        $descricao_guest_bonus = $guest["descricao_bonus"] . " - " . euro($guest["comissao_bonus"]);
                        $return['guest']["descricao_bonus"] = $return['guest']['descricao_bonus'] ? $return['guest']['descricao_bonus'] . " </br> " . $descricao_guest_bonus : "" . $descricao_guest_bonus;
                    }
                    if ($guest_team['descricao']) {
                        $return['guest_team']['comissao'] += $guest_team["comissao"];
                        $descricao_guest_team = $guest_team["descricao"] . " - " . euro($guest_team["comissao"]);
                        $return['guest_team']["descricao"] = $return['guest_team']['descricao'] ? $return['guest_team']['descricao'] . " </br> " . $descricao_guest_team : "" . $descricao_guest_team;
                    }
                    if ($guest_team['comissao_bonus'] > 0) {
                        $return['guest_team']['comissao_bonus'] += $guest_team["comissao_bonus"];
                        $descricao_guest_team_bonus = $guest_team["descricao_bonus"] . " - " . euro($guest_team["comissao_bonus"]);
                        $return['guest_team']["descricao_bonus"] = $return['guest_team']['descricao_bonus'] ? $return['guest_team']['descricao_bonus'] . " </br> " . $descricao_guest_team_bonus : "" . $descricao_guest_team_bonus;
                    }
                    if ($privados['comissao'] > 0) {
                        $return['privados']['comissao'] += $privados["comissao"];
                        $descricao_privados = $privados["descricao"] . " - " . euro($privados["comissao"]);
                        $return['privados']["descricao"] = $return['privados']['descricao'] ? $return['privados']['descricao'] . " </br> " . $descricao_privados : "" . $descricao_privados;
                    }
                    if ($privados_chefe['comissao'] > 0) {
                        $return['privados_chefe']['comissao'] += $privados_chefe["comissao"];
                        $descricao_privados = $privados_chefe["descricao"] . " - " . euro($privados_chefe["comissao"]);
                        $return['privados_chefe']["descricao"] = $return['privados_chefe']['descricao'] ? $return['privados_chefe']['descricao'] . " </br> " . $descricao_privados : "" . $descricao_privados;
                    }

                    if ($atrasos['comissao'] > 0) {
                        $return['atrasos']['comissao'] += $atrasos["comissao"];
                        $descricao_atrasos = $atrasos["descricao"] . " - " . euro($atrasos["comissao"]);
                        $return['atrasos']["descricao"] = $return['atrasos']['descricao'] ? $return['atrasos']['descricao'] . " </br> " . $descricao_atrasos : "" . $descricao_atrasos;
                    }
                    // if ($convites['comissao'] > 0) {
                    //     $return['convites']['comissao'] += $convites["comissao"];
                    //     $descricao_convites = $convites["descricao"] . " - " . euro($convites["comissao"]);
                    //     $return['convites']["descricao"] = $return['convites']['descricao'] ? $return['convites']['descricao'] . " </br> " . $descricao_convites : "" . $descricao_convites;
                    // }
                    if ($sessoes['comissao'] > 0) {
                        $return['sessoes']['comissao'] += $sessoes["comissao"];
                        $descricao_sessoes = $sessoes["descricao"] . " - " . euro($sessoes["comissao"]);
                        $return['sessoes']["descricao"] = $return['sessoes']['descricao'] ? $return['sessoes']['descricao'] . " </br> " . $descricao_sessoes : "" . $descricao_sessoes;
                    }
                }
            }
			if ($garrafas) {
				foreach ($garrafas as $garrafa) {
					$return['garrafas']['comissao'] += $garrafa["comissao"];
					$descricao_garrafas = $garrafa["descricao"] . " - " . euro($garrafa["comissao"]);
					$return['garrafas']["descricao"] = $return['garrafas']['descricao'] ? $return['garrafas']['descricao'] . " </br> " . $descricao_garrafas : "" . $descricao_garrafas;
				}
			}

            $chefe_dia_melhor = $this->devolvePremiosMelhorChefe($id_rp);

			if ($chefe_dia_melhor) {
				foreach ($chefe_dia_melhor as $estatistica) {
					$return['estatisticas_chefe']['comissao'] += $estatistica["comissao"];
					$descricao_estatisticas = $estatistica["descricao"] . " - " . euro($estatistica["comissao"]);
					$return['estatisticas_chefe']["descricao"] = $return['estatisticas_chefe']['descricao'] ? $return['estatisticas_chefe']['descricao'] . " </br> " . $descricao_estatisticas : "" . $descricao_estatisticas;
					$return['estatisticas_chefe']["id"][] = $estatistica["id"];
				}
			}

            $rp_dia_melhor = $this->devolvePremiosMelhorRP($id_rp);
			if ($rp_dia_melhor) {
				foreach ($rp_dia_melhor as $estatistica) {
					$return['estatisticas_rp']['comissao'] += $estatistica["comissao"];
					$descricao_estatisticas = $estatistica["descricao"] . " - " . euro($estatistica["comissao"]);
					$return['estatisticas_rp']["descricao"] = $return['estatisticas_rp']['descricao'] ? $return['estatisticas_rp']['descricao'] . " </br> " . $descricao_estatisticas : "" . $descricao_estatisticas;
					$return['estatisticas_rp']["id"][] = $estatistica["id"];
				}
			}
            $privados_semana_melhor = $this->devolvePremiosMelhorPrivados($id_rp);

			if ($privados_semana_melhor) {
				foreach ($privados_semana_melhor as $estatistica) {
					$return['estatisticas_privados']['comissao'] += $estatistica["comissao"];
					$descricao_estatisticas = $estatistica["descricao"] . " - " . euro($estatistica["comissao"]);
					$return['estatisticas_privados']["descricao"] = $return['estatisticas_privados']['descricao'] ? $return['estatisticas_privados']['descricao'] . " </br> " . $descricao_estatisticas : "" . $descricao_estatisticas;
					$return['estatisticas_privados']["id"][] = $estatistica["id"];
				}
			}

			if ($divida < 0) {
				$return['divida'] = $divida;
			}

            $extras = $this->devolveValoresExtras($id_rp);
            if($extras){
                $return['extras'] = $extras;
            }

			$return['total'] = $return['estatisticas_privados']['comissao'] + $return['estatisticas_rp']['comissao'] + $return['estatisticas_chefe']['comissao'] + $return['sessoes']['comissao'] + $return['guest']['comissao'] + $return['guest']['comissao_bonus'] + $return['guest_team']['comissao'] + $return['guest_team']['comissao_bonus'] + $return['privados']['comissao'] + $return['privados_chefe']['comissao'] + $return['garrafas']['comissao'] - $return['convites']['comissao'] - $return['atrasos']['comissao'] + $return['extras']['total'] + ($return['divida']);

			return $return;
        }
    }
    function devolvePosicaoMelhorChefe($id_rp, $data_evento) {
        $sql = "SELECT * FROM estatisticas_entradas_chefe WHERE id_rp = " . $id_rp . " AND data_evento = '" . $data_evento . "'";
        $resultado = $this->db->query($sql);
        return $resultado[0];
    }
    function devolvePremiosMelhorChefe($id_rp) {
        $sql = "SELECT * FROM estatisticas_entradas_chefe WHERE id_rp = " . $id_rp . " AND posicao IN (1, 2, 3) AND pago = 0 AND realtime = 0";
        $resultado = $this->db->query($sql);

        if ($resultado) {
            $return = array();
            foreach ($resultado as $k => $res) {

                $premio = 0;
                $return[$k]['descricao'] .= "<b>" . $res["posicao"] . "º lugar (" . $res['data_evento'] . ")</b>: " . intval($res['entradas']) . " entradas";
                switch ($res["posicao"]) {
                    case 1:
                        $premio = 15;
                        break;
                    case 2:
                        $premio = 10;
                        break;
                    case 3:
                        $premio = 5;
                        break;
                  }
                $return[$k]['comissao'] = $premio;
                $return[$k]['id'] = $res["id"];
            }
            return $return;
        }

    }
    function devolvePosicaoMelhorRP($id_rp, $data_evento) {
        $sql = "SELECT * FROM estatisticas_entradas_rp WHERE id_rp = " . $id_rp . " AND data_evento = '" . $data_evento . "'";
        $resultado = $this->db->query($sql);
        return $resultado[0];
    }
    function devolvePremiosMelhorRP($id_rp) {

        $sql = "SELECT * FROM estatisticas_entradas_rp WHERE id_rp = " . $id_rp . " AND posicao IN (1, 2, 3) AND pago = 0 AND realtime = 0";
        $resultado = $this->db->query($sql);

        if ($resultado) {
            $return = array();
            foreach ($resultado as $k => $res) {

                $premio = 0;
                $return[$k]['descricao'] .= "<b>" . $res["posicao"] . "º lugar (" . $res['data_evento'] . ")</b>: " . intval($res['entradas']) . " entradas";
                switch ($res["posicao"]) {
                    case 1:
                        $premio = 15;
                        break;
                    case 2:
                        $premio = 10;
                        break;
                    case 3:
                        $premio = 5;
                        break;
                  }
                $return[$k]['comissao'] = $premio;
                $return[$k]['id'] = $res["id"];
            }
            return $return;
        }
    }
    function devolvePosicaoMelhorPrivados($id_rp, $data_evento) {
        $sql = "SELECT * FROM estatisticas_privados_semana_rp WHERE id_rp = " . $id_rp . " AND semana_de <= '" . $data_evento . "' AND semana_ate >= '" . $data_evento . "'";
        $resultado = $this->db->query($sql);
        return $resultado[0];
    }
    function devolvePremiosMelhorPrivados($id_rp) {
        $sql = "SELECT * FROM estatisticas_privados_semana_rp WHERE id_rp = " . $id_rp . " AND posicao IN (1, 2, 3) AND pago = 0 AND realtime = 0";
        $resultado = $this->db->query($sql);

        if ($resultado) {
            $return = array();
            foreach ($resultado as $k => $res) {

                $premio = 0;
                $return[$k]['descricao'] .= "<b>" . $res["posicao"] . "º lugar (" . $res['semana_de'] . " até " . $res['semana_ate'] . ")</b>: " . euro($res['total']) . " na soma do valor base";
                switch ($res["posicao"]) {
                    case 1:
                        $premio = 60;
                        break;
                    case 2:
                        $premio = 30;
                        break;
                    case 3:
                        $premio = 15;
                        break;
                  }
                $return[$k]['comissao'] = $premio;
                $return[$k]['id'] = $res["id"];
            }
            return $return;
        }
    }

    function devolveValidaConvite($id_rp, $data_evento)
    {
        $query = "SELECT convites_pagamentos FROM rps INNER JOIN rps_cargos ON rps.id_cargo = rps_cargos.id  WHERE rps.id = " . $id_rp . " AND rps_cargos.convites_pagamentos = 1 AND rps.penaliza_convite = 1";
        $resultado_rp_cargos = $this->db->query($query);
        if ($resultado_rp_cargos[0]['convites_pagamentos'] == 1) {
            $query = "SELECT id, imagem, valido FROM convites WHERE convites.id_rp = " . $id_rp . " AND convites.data_evento = '" . $data_evento . "'   GROUP BY convites.data_evento DESC";
            $resultado = $this->db->query($query);

            if (empty($resultado)) {
                $eventos_return['comissao'] = 5;
                $eventos_return['descricao'] = "<b>" . $data_evento . "</b>: " . "Não foi feito o upload para este evento pelo staff.";
                return $eventos_return;
            } else {
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/convites/" . $resultado[0]['imagem']) && $resultado[0]['imagem']) {
                    if ($resultado[0]['valido'] == 2) {
                        $eventos_return['comissao'] = 5;
                        $eventos_return['descricao'] = "<b>" . $data_evento . "</b>: " . "O convite não foi aceite.";
                        return $eventos_return;
                    }
                } else {
                    $eventos_return['comissao'] = 5;
                    $eventos_return['descricao'] = "<b>" . $data_evento . "</b>: " . "Não foi feito o upload para este evento pelo staff.";
                    return $eventos_return;
                }
            }
        }
    }

    function devolveConvites($id_rp)
    {

        $where = "";
        $datas = $this->devolveDatasParaPagamento($id_rp);
        if ($datas) {
            foreach ($datas as $data) {
                $datas_a_pagar[] = $data['data_evento'];
            }
            $where .= " AND data_evento  IN('" . implode("', '", $datas_a_pagar) . "') ";

            $query = "SELECT id, imagem, valido, data, data_evento FROM convites WHERE convites.id_rp = " . $id_rp . "  $where";
            $resultado = $this->db->query($query);
            foreach ($resultado as $k => $res) {
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/fotos/convites/" . $res['imagem']) && $res['imagem']) {
                    $resultado[$k]['imagem'] = "/fotos/convites/" . $res['imagem'];
                }
            }
        }
        return $resultado;
    }
    function devolveConviteFicheiro($ficheiro = false)
    {

        if ($ficheiro) {
            $query = "SELECT convites.id, convites.data_evento FROM convites WHERE convites.imagem= '" . $ficheiro . "'";
            $resultado = $this->db->query($query);

            if (!empty($resultado)) {
                return $resultado[0];
            }

        }

    }
    function devolveAtrasos($id_rp, $data_evento)
    {

        $query = "SELECT regras_atrasos_pagamentos FROM rps INNER JOIN rps_cargos ON rps.id_cargo = rps_cargos.id  WHERE rps.id = " . $id_rp . " AND rps_cargos.regras_atrasos_pagamentos = 1";
        $resultado_rp_cargos = $this->db->query($query);
        if ($resultado_rp_cargos[0]['regras_atrasos_pagamentos'] == 1) {
            $query = "SELECT data_evento, data_entrada FROM presencas WHERE presencas.id_rp = " . $id_rp . " AND presencas.data_evento = '" . $data_evento . "'  GROUP BY presencas.data_evento DESC";
            $resultado = $this->db->query($query);
            if (!empty($resultado)) {
                $hora_entrada = date('H:i:s', strtotime($resultado[0]['data_entrada']));
                if ($hora_entrada) {
                    $query = "SELECT valor FROM logica_atrasos WHERE (logica_atrasos.de <= '" . $hora_entrada . "' AND logica_atrasos.ate >= '" . $hora_entrada . "')  ";
                    $resultado = $this->db->query($query);

                    $eventos_return['descricao'] = "<b>" . $data_evento . "</b>: " . $hora_entrada;
                    $eventos_return['comissao'] = $resultado[0]['valor'];
                    return $eventos_return;
                }
            }
        }
    }

    function devolveComissaoPrivados($id_rp, $data_evento)
    {
        $query = "SELECT count(f.data_evento) as quantidade, f.id_mesa as id_mesa, SUM(f.total) as total, f.data_evento, f.id_cargo FROM (SELECT venda_privados.id as id, count(venda_privados.data_evento) as quantidade, venda_privados.id_mesa as id_mesa, (privados_salas_mesas_disponibilidade.valor) as total, venda_privados.data_evento as data_evento, rps.id_cargo FROM venda_privados INNER JOIN rps ON rps.id = venda_privados.id_rp INNER JOIN privados_salas_mesas_disponibilidade ON venda_privados.id_reserva  = privados_salas_mesas_disponibilidade.id  WHERE venda_privados.id_rp = " . $id_rp . " AND venda_privados.data_evento =  '" . $data_evento . "' GROUP BY venda_privados.data_evento, venda_privados.id_mesa ORDER BY venda_privados.id ASC) f  GROUP BY f.data_evento  ORDER BY f.id ASC
        ";
        $resultado = $this->db->query($query);

        if ($resultado) {
			//chefe de equipa recebe 10%
			if($resultado[0]['id_cargo'] == 20){
				$return['comissao'] = ($resultado[0]['total'] / 1.23) * 0.10;
			}
			else{
				$return['comissao'] = ($resultado[0]['total'] / 1.23) * 0.08;
            }
			$return['descricao'] = "<b>" . $data_evento . " (Total C/IVA: " . $resultado[0]['total'] . " €) </b>: " . intval($resultado[0]['quantidade']) . " privados";
            $return['total'] = $resultado[0]['total'];
            $return['numero'] = intval($resultado[0]['quantidade']);


            return $return;
        }
    }
    function devolveComissaoPrivadosChefe($id_rp, $data_evento)
    {
        $query = "SELECT count(f.data_evento) as quantidade, f.id_mesa as id_mesa, SUM(f.total) as total, f.data_evento FROM (SELECT venda_privados.id as id, count(venda_privados.data_evento) as quantidade, venda_privados.id_mesa as id_mesa, (privados_salas_mesas_disponibilidade.valor) as total, venda_privados.data_evento as data_evento FROM venda_privados INNER JOIN rps ON rps.id = venda_privados.id_rp INNER JOIN privados_salas_mesas_disponibilidade ON venda_privados.id_reserva  = privados_salas_mesas_disponibilidade.id  WHERE rps.id_chefe_equipa = " . $id_rp . " AND venda_privados.data_evento =  '" . $data_evento . "' GROUP BY venda_privados.data_evento, venda_privados.id_mesa ORDER BY venda_privados.id ASC) f  GROUP BY f.data_evento  ORDER BY f.id ASC
            ";
        $resultado = $this->db->query($query);

        if ($resultado) {
            $return['comissao'] = ($resultado[0]['total'] / 1.23) * 0.02;
            $return['descricao'] = "<b>" . $data_evento . "</b>: " . intval($resultado[0]['quantidade']) . " privados equipa";
            $return['total'] = $resultado[0]['total'];
            $return['numero'] = $resultado[0]['quantidade'];

            return $return;
        }
    }
    function devolveComissaoSessoesChefe($id_rp, $data_evento, $entrou = 0)
    {

        $query = " SELECT COUNT(rps.id) as conta FROM rps INNER JOIN presencas ON rps.id = presencas.id_rp  WHERE rps.id = '" . $id_rp . "' AND presencas.data_evento = '" . $data_evento  . "' AND (rps.id_cargo = 20) GROUP BY rps.id";
        $resultado = $this->db->query($query);


        if ($resultado[0]["conta"] > 0) {

            $comissao = 0;

            switch (true) {
                case $entrou >= 20 && $entrou < 40:
                    $comissao = 20;
                    break;
                case $entrou >= 40 && $entrou < 50:
                    $comissao = 40;
                    break;
                case $entrou >= 50:
                    $comissao = 50;
                    break;
            }

            $return['comissao'] =  $comissao;
            $return['descricao'] = "<b>" . $data_evento . "</b>: Sessão (" . intval($entrou) . " entradas) ";

            return $return;
        }
    }
    function devolveComissaoGarrafas($id_rp)
    {
        $query = "SELECT sum(venda_garrafas_bar_garrafas.quantidade) as quantidade, venda_garrafas_bar.data_evento, SUM(venda_garrafas_bar.total) as total FROM venda_garrafas_bar INNER JOIN venda_garrafas_bar_garrafas ON venda_garrafas_bar_garrafas.id_compra = venda_garrafas_bar.id  INNER JOIN garrafas ON venda_garrafas_bar_garrafas.id_garrafa = garrafas.id AND garrafas.comissao = 1 WHERE venda_garrafas_bar.id_rp = " . $id_rp . " AND venda_garrafas_bar.paga = '0' AND venda_garrafas_bar.total > 50 GROUP BY venda_garrafas_bar.data_evento DESC";
        $resultado = $this->db->query($query);
        if ($resultado) {
            foreach ($resultado as $k => $res) {

                $return[$k]['comissao'] = $res['total'] * 0.05;
                $return[$k]['descricao'] .= "<b>" . $res['data_evento'] . "</b>: " . intval($res['quantidade']) . " garrafas";
            }
            return $return;
        }
    }
    function devolveNomeRP($id_rp)
    {
        $query = "SELECT nome FROM rps  WHERE  id = " . $id_rp . "";
        $eventoRP = $this->db->query($query);
        return $eventoRP[0]['nome'];
    }
    function devolveNomeAdministrador($id)
    {
        $query = "SELECT nome FROM administradores  WHERE  id = " . $id . "";
        $eventoRP = $this->db->query($query);
        return $eventoRP[0]['nome'];
    }
    function devolveSessaoRP($id_rp)
    {
        if ($id_rp) {
            $query = "SELECT salario FROM rps  WHERE  id = '" . $id_rp . "'";
            $eventoRP = $this->db->query($query);
            return $eventoRP[0]['salario'];
        }
    }
    function devolveExtras($id_rp, $sessao)
    {
        $query = "SELECT * FROM pagamentos_extras  WHERE  id_rp = '" . $id_rp . "' AND sessao =  '" . $sessao . "'";
        $eventoRP = $this->db->query($query);

        return $eventoRP;
    }
    function devolveValoresExtras($id_rp)
    {

        $query = "SELECT id, valor, descricao, nome, tipo FROM pagamentos_extras  WHERE  id_rp = '" . $id_rp . "'";
        $valores_extras = $this->db->query($query);

        if ($valores_extras) {
            $extras['items'] = $valores_extras;
            $extras['total'] = 0;
            foreach ($valores_extras as $ve) {
                $extras['total'] += $ve['valor'];
            }
        }


        return $extras;
    }
    function apagaExtras($id_rp)
    {
        $query = "DELETE FROM pagamentos_extras  WHERE  id_rp = '" . $id_rp . "'";
        return $this->db->query($query);
    }

    function devolveContaCorrente($id_rp)
    {

        $query = "SELECT id FROM conta_corrente  WHERE id_rp = '" . $id_rp . "'";
        $conta_corrente = $this->db->query($query);
        return $conta_corrente[0]['id'];
    }
    function devolveDividaRP($id_rp)
    {
        $query = "SELECT total FROM conta_corrente  WHERE id_rp = '" . $id_rp . "' ORDER BY data DESC";
        $divida = $this->db->query($query);

        if ($divida[0]['total'] < 0) {
            return $divida[0]['total'];
        }
    }

    function devolveContaCorrenteID($id)
    {
        $query = "SELECT * FROM conta_corrente  WHERE id = '" . $id . "'";
        $conta_corrente = $this->db->query($query);
        return $conta_corrente[0];
    }
    function devolveValoresCaixas($data)
    {
        $query = "SELECT * FROM conta_corrente_caixas  WHERE data = '" . $data . "' ORDER BY numero ASC";
        $res = $this->db->query($query);
        return $res;
    }
    function apagaValoresCaixas($data)
    {
        $query = "DELETE FROM conta_corrente_caixas  WHERE data = '" . $data . "'";
        $res = $this->db->query($query);
        return $res;
    }
    function listaPagamentosCaixaData($data = false)
    {
        $where = "";
        if ($data) {
            $where .= " WHERE data_evento = '" . $data . "' ";
        }

        $query = "SELECT data_evento as data FROM conta_corrente $where   GROUP BY data_evento DESC";
        $pagamentos = $this->db->query($query);
        if ($pagamentos) {
            foreach ($pagamentos as $k => $pagamento) {
                $pagamentos[$k]['total_caixa'] = $this->devolveTotalCaixa($pagamento['data']);
                $pagamentos[$k]['total_pagamentos'] = $this->devolveTotalPago($pagamento['data'], 1);
                $pagamentos[$k]['total_pagamentos_scaixa'] = $this->devolveTotalPago($pagamento['data'], 0);
                $pagamentos[$k]['total_recebido'] = $this->devolveTotalRecebido($pagamento['data']);
                $pagamentos[$k]['diferenca'] = $pagamentos[$k]['total_caixa'] - $pagamentos[$k]['total_pagamentos'];
            }
            return $pagamentos;
        }
    }

    function devolveDiferencaTotalCaixa($data)
    {
        $pagamentos['total_caixa'] = $this->devolveTotalCaixa($data);
        $pagamentos['total_pagamentos'] = $this->devolveTotalPago($data, 1);
        return $pagamentos['total_caixa'] - $pagamentos['total_pagamentos'];
    }

    function devolveTotalCaixa($data)
    {
        $query = "SELECT sum(valor) as total FROM conta_corrente_caixas WHERE data = '" . $data . "'  GROUP BY data";
        $res = $this->db->query($query);
        return $res[0]['total'];
    }
    function devolveTotalPago($data, $caixa = 1)
    {
        $where = "";
        if ($data) {
            $where .= " AND data_evento = '" . $data . "' ";
        }
        $query = "SELECT sum(total) as total FROM conta_corrente WHERE total > 0 $where AND pagamento_caixa = '" . $caixa . "' GROUP BY data_evento";
        $res = $this->db->query($query);
        return $res[0]['total'];
    }
    function devolveTotalRecebido($data)
    {
        $where = "";
        if ($data) {
            $where .= " AND data_evento = '" . $data . "' ";
        }
        $query = "SELECT sum(total) as total FROM conta_corrente WHERE total < 0 $where GROUP BY data_evento";
        $res = $this->db->query($query);
        return $res[0]['total'];
    }
    function listaCaixasData($data)
    {
        // conta_corrente_caixas

        $query = "SELECT numero, valor FROM conta_corrente_caixas WHERE data = '" . $data . "'";
        $res = $this->db->query($query);
        return $res;
    }
    function devolveFiltrosPagamentosCaixa($data, $filtro)
    {

        $query = "";
        $query_inner = "";
        if ($filtro) {
            if ($filtro['nome']) {
                $query_inner .= " LEFT JOIN rps ON rps.id = conta_corrente.id_rp ";
                $query .= " AND (rps.nome like '%" . $filtro['nome'] . "%' OR conta_corrente.nome like '%" . $filtro['nome'] . "%') ";
            }
            if (strlen($filtro['pagamento_caixa']) > 0) {
                $query .= " AND conta_corrente.pagamento_caixa = " . $filtro['pagamento_caixa'];
            }
            if ($filtro['id_administrador']) {
                $query .= " AND conta_corrente.id_administrador = " . $filtro['id_administrador'];
            }
        }

        $query_administradores = "SELECT administradores.id, administradores.nome FROM conta_corrente INNER JOIN administradores ON administradores.id = conta_corrente.id_administrador $query_inner WHERE conta_corrente.data_evento = '" . $data . "' $query GROUP by conta_corrente.id_administrador";
        $filtros['administradores'] = $this->db->query($query_administradores);

        $query_pagamento = "SELECT conta_corrente.pagamento_caixa, IF(conta_corrente.pagamento_caixa = 0, 'Não', 'Sim') as nome FROM conta_corrente  $query_inner WHERE conta_corrente.data_evento = '" . $data . "' $query GROUP BY conta_corrente.pagamento_caixa";
        $filtros['pagamento_caixa'] = $this->db->query($query_pagamento);
        return $filtros;

    }
    function listaExcellPagamentosLinhas($data)
    {

        $query = "SELECT IF(conta_corrente.nome = '', rps.nome, conta_corrente.nome) as nome_staff, administradores.nome as nome_administrador, IF(conta_corrente.pagamento_caixa = 0, 'Não', 'Sim') as pagamento_caixa,  conta_corrente_linhas.nome as titulo_pagamento, conta_corrente_linhas.descricao  as descricao_pagamento, conta_corrente_linhas.valor FROM conta_corrente_linhas INNER JOIN conta_corrente ON conta_corrente_linhas.id_conta_corrente = conta_corrente.id LEFT JOIN rps ON rps.id = conta_corrente.id_rp INNER JOIN administradores ON administradores.id = conta_corrente.id_administrador  WHERE conta_corrente.data_evento = '" . $data . "'  ORDER BY conta_corrente.data ASC";
        $res = $this->db->query($query);
        return $res;

    }
    function listaExcellPagamentosCaixa($data, $filtro = false)
    {
        $query = "";
        $query_inner = "";
        if ($filtro) {
            if ($filtro['nome']) {
                $query_inner .= " LEFT JOIN rps ON rps.id = conta_corrente.id_rp ";
                $query .= " AND (rps.nome like '%" . $filtro['nome'] . "%'  OR conta_corrente.nome like '%" . $filtro['nome'] . "%') ";
            }
            if (strlen($filtro['pagamento_caixa']) > 0) {
                $query .= " AND conta_corrente.pagamento_caixa = " . $filtro['pagamento_caixa'];
            }
            if ($filtro['id_administrador']) {
                $query .= " AND conta_corrente.id_administrador = " . $filtro['id_administrador'];

            }
        }

        $query = "SELECT conta_corrente.id, conta_corrente.id_administrador, conta_corrente.pagamento_caixa, conta_corrente.nome, conta_corrente.tipo, conta_corrente.id_rp, conta_corrente.data, conta_corrente.total FROM conta_corrente $query_inner WHERE conta_corrente.data_evento = '" . $data . "' $query";
        $res = $this->db->query($query);
        foreach ($res as $k => $rs) {
            if ($rs['id_rp']) {
                $res[$k]['nome'] = $this->devolveNomeRP($rs['id_rp']);
            }
            if ($rs['id_administrador']) {
                $res[$k]['nome_administrador'] = $this->devolveNomeAdministrador($rs['id_administrador']);
            }
            if (empty($rs['tipo']) && $rs['id_rp']) {
                $res[$k]['tipo'] = "Pagamento Staff";
            }
        }
        return $res;
    }
    function listaPresencasEventos($filtros = false, $limit = false)
    {
        $query = "SELECT data_evento, count(*) as conta FROM presencas GROUP BY data_evento ORDER BY data_evento DESC $limit";
        $res = $this->db->query($query);
        return $res;
    }
    function contaPresencasEventos($filtros = false)
    {
        $query = "SELECT count(DISTINCT data_evento)  as conta FROM presencas  ORDER BY data_evento DESC";
        $res = $this->db->query($query);
        return $res[0]['conta'];
    }

    function listaPresencasEventosData($data, $filtro = false)
    {

        $query_search = "";
        $query_inner = "";
        if ($filtro) {
            if ($filtro['nome']) {
                $query_inner .= " LEFT JOIN rps ON rps.id = presencas.id_rp ";
                $query_search .= " AND ( rps.nome like '%" . $filtro['nome'] . "%' OR presencas.nome like '%" . $filtro['nome'] . "%' )";
            }
            if ($filtro['numero_cartao']) {
                $query_search .= " AND presencas.numero_cartao = " . $filtro['numero_cartao'];
            }
            if ($filtro['data_de']) {
                $query_search .= " AND presencas.data_entrada >= '" . $filtro['data_de'] . "'";
            }
            if ($filtro['data_ate']) {
                $query_search .= " AND presencas.data_entrada <= '" . $filtro['data_ate'] . "'";
            }
        }

        $query = "SELECT presencas.data_evento, presencas.nome, presencas.data_entrada, presencas.numero_cartao, presencas.id_rp, presencas.id FROM presencas  $query_inner WHERE presencas.data_evento = '" . $data . "'  $query_search ORDER BY presencas.data_entrada DESC";
        $res = $this->db->query($query);
        foreach ($res as $k => $rs) {
            if (empty($rs['nome'])) {
                $res[$k]['nome'] = $this->devolveNomeRP($rs['id_rp']);
            }
        }
        return $res;
    }
    function apagaContaCorrente($id)
    {
        if (intval($id) > 0) {

            $query = "SELECT tabela, tabela_id FROM conta_corrente_linhas WHERE id_conta_corrente = '" . $id . "' AND tabela != ''";
            $res = $this->db->query($query);

            if($res){
                foreach ($res as $k => $campo) {
                    if($campo["tabela_id"]) {
                        $tabela_array = explode(",", $campo["tabela_id"]);
                        if($tabela_array) {
                            foreach( $tabela_array as $key => $value) {
                                $this->db->update($campo["tabela"], array("pago" => 0), "id = " . $value);
                            }
                        }
                    }
                }
            }


            $query = "DELETE FROM conta_corrente WHERE id = '" . $id . "'";
            $res = $this->db->query($query);

            $query = "DELETE FROM conta_corrente_eventos WHERE id_conta_corrente = '" . $id . "'";
            $res = $this->db->query($query);

            $query = "DELETE FROM conta_corrente_linhas WHERE id_conta_corrente = '" . $id . "'";
            $res = $this->db->query($query);
        }
        return $res;
    }
    function listaLinhasContaCorrente($id)
    {
        $query = "SELECT nome, descricao, valor FROM conta_corrente_linhas WHERE id_conta_corrente = '" . $id . "'  ORDER BY id ASC";
        $res = $this->db->query($query);
        return $res;
    }

    function listaConvitesEventos($filtros = false, $limit = false)
    {
        $query = "SELECT data_evento, count(*) as conta FROM convites GROUP BY data_evento ORDER BY data_evento DESC $limit";
        $res = $this->db->query($query);
        return $res;
    }

    function contaConvitesEventos($filtros = false)
    {
        $query = "SELECT  count(DISTINCT data_evento) as conta FROM convites ORDER BY data_evento DESC";
        $res = $this->db->query($query);
        return $res[0]['conta'];
    }

    function listaConvitesEventosData($data)
    {
        $query = "SELECT data_evento, data, imagem, id_rp, id, valido FROM convites WHERE data_evento = '" . $data . "'  ORDER BY data DESC";
        $res = $this->db->query($query);
        foreach ($res as $k => $rs) {
            $res[$k]['nome'] = $this->devolveNomeRP($rs['id_rp']);
        }

        return $res;
    }
    function devolveConvite($id)
    {
        $query = "SELECT data_evento, data, imagem, id_rp, id, valido FROM convites WHERE id = '" . $id . "' ";
        $res = $this->db->query($query);
        foreach ($res as $k => $rs) {
            $res[$k]['nome'] = $this->devolveNomeRP($rs['id_rp']);
        }
        return $res[0];
    }
    function verificaErro($id)
    {

        $query = "SELECT total FROM conta_corrente WHERE id = " . $id;
        $res = $this->db->query($query);
        $total = $res[0]['total'];


        $query = "SELECT sum(valor) as total FROM conta_corrente_linhas WHERE id_conta_corrente = '" . $id . "'  ORDER BY id ASC";
        $res = $this->db->query($query);
        $total_real = $res[0]['total'];
        if ($total_real != $total) {
            return 1;
        } else {
            return 0;
        }
    }
    function descontaValorCaixa()
    {
        $query = "SELECT pagamento_caixa FROM administradores WHERE id = '" . $_SESSION['id_utilizador'] . "' ";
        $res = $this->db->query($query);

        return $res[0]['pagamento_caixa'];
    }
}