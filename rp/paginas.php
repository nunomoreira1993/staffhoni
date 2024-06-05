<?php
if (isset($_GET['pg']))
	$pg = $_GET['pg'];
else
	$pg = "";

switch ($pg) {

	case 'alterar_password':
		include "alterar_password.php";
		break;

	case 'alterar_foto':
		include "alterar_foto.php";
		break;

	case 'cartoes_sem_consumo':
		include "cartoes_sem_consumo.php";
		break;

	case 'adicionar_cartoes_sem_consumo':
		include "adicionar_cartoes_sem_consumo.php";
		break;

	case 'disponibilidade_de_mesas':
		include $_SERVER['DOCUMENT_ROOT'] . "/rp/privados/disponibilidade_de_mesas.php";
		break;

	case 'lista_espera_mesas':
		include $_SERVER['DOCUMENT_ROOT'] . "/rp/privados/lista_espera_mesas.php";
		break;

	case 'adicionar_lista_espera_mesa':
		include $_SERVER['DOCUMENT_ROOT'] . "/rp/privados/adicionar_lista_espera_mesa.php";
		break;

	case 'inserir_reserva':
		include $_SERVER['DOCUMENT_ROOT'] . "/rp/privados/inserir_reserva.php";
		break;

	case 'pagamento_adiantado':
		include $_SERVER['DOCUMENT_ROOT'] . "/rp/privados/pagamento_adiantado.php";
		break;

	case 'reserva_garrafas':
		include $_SERVER['DOCUMENT_ROOT'] . "/rp/privados/reserva_garrafas.php";
		break;

	case 'eventos_produtores_privados':
		include $_SERVER['DOCUMENT_ROOT'] . "/rp/privados/eventos_produtores_privados.php";
		break;

	case 'adicionar_reserva_garrafa':
		include $_SERVER['DOCUMENT_ROOT'] . "/rp/privados/adicionar_reserva_garrafa.php";
		break;

	case 'cartoes_consumo_obrigatorio':
		include "cartoes_consumo_obrigatorio.php";
		break;

	case 'adicionar_cartoes_consumo_obrigatorio':
		include "adicionar_cartoes_consumo_obrigatorio.php";
		break;

	case 'convites':
		include "convites.php";
		break;

	case 'eventos_produtores':
		include "eventos_produtores.php";
		break;

	case 'eventos_equipa':
		include "eventos_equipa.php";
		break;

	case 'eventos_produtores_equipas':
		include "eventos_produtores_equipas.php";
		break;

	case 'eventos_equipas_rps':
		include "eventos_equipas_rps.php";
		break;

	case 'adicionar_convites':
		include "adicionar_convites.php";
		break;

	case 'historico_pagamentos':
		include "historico_pagamentos.php";
		break;


	default:
		include  "homepage.php";
		break;
}
