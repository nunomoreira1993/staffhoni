<?php 
class validacao {
    function validacao($db) {
        $this->cDb = $db;
    }
	function existeTraducao($tag, $nome){
		$query = "SELECT id FROM traducoes WHERE tag='$tag' AND nome='$nome'";
		$res = $this->cDb->abreCursor($query);
		if($res){
			return $res[0]['id'];
		}else{
			return 0;
		}	
	}
	function devolveConfig($numero, $lingua){
		// REQUIRED
		$campos[0]['tag'] = "txt_validacao";
		$campos[0]['nome'] = "required";
		$campos[0]['lingua']['pt'] = "O campo {CAMPO} é obrigatório.";
		$campos[0]['lingua']['en'] = "The field {CAMPO} is required.";
		$campos[0]['lingua']['default'] = "The field {CAMPO} is required.";

		//EMAIL
		$campos[1]['tag'] = "txt_validacao";
		$campos[1]['nome'] = "email";
		$campos[1]['lingua']['pt'] = "O campo {CAMPO} não é um e-mail válido.";
		$campos[1]['lingua']['en'] = "The field {CAMPO} is not a valid e-mail.";
		$campos[1]['lingua']['default'] = "The field {CAMPO} is not a valid e-mail .";

		//Inteiros
		$campos[2]['tag'] = "txt_validacao";
		$campos[2]['nome'] = "inteiro";
		$campos[2]['lingua']['pt'] = "O campo {CAMPO} não é numérico.";
		$campos[2]['lingua']['en'] = "The field {CAMPO} is not numeric.";
		$campos[2]['lingua']['default'] = "The field {CAMPO} is not numeric.";

		//maxlenght
		$campos[3]['tag'] = "txt_validacao";
		$campos[3]['nome'] = "maxlenght";
		$campos[3]['lingua']['pt'] = "O campo {CAMPO} tem de ter no máximo {NUMERO} caracteres.";
		$campos[3]['lingua']['en'] = "The field {CAMPO} must have no more than 9 characters.";
		$campos[3]['lingua']['default'] = "The field {CAMPO} must have no more than 9 characters.";

		//minlenght
		$campos[4]['tag'] = "txt_validacao";
		$campos[4]['nome'] = "minlenght";
		$campos[4]['lingua']['pt'] = "O campo {CAMPO} tem de ter no mínimo {NUMERO} caracteres.";
		$campos[4]['lingua']['en'] = "The field {CAMPO} must be at least 9 characters.";
		$campos[4]['lingua']['default'] = "The field {CAMPO} must be at least 9 characters.";

		//datamysql
		$campos[5]['tag'] = "txt_validacao";
		$campos[5]['nome'] = "datamysql";
		$campos[5]['lingua']['pt'] = "O campo {CAMPO} tem de ter o formato 'AAAA-MM-DD'.";
		$campos[5]['lingua']['en'] = "The field {CAMPO} must have the format ' YYYY-MM-DD'.";
		$campos[5]['lingua']['default'] = "The field {CAMPO} must have the format ' YYYY-MM-DD'.";
		
		//maiorque
		$campos[6]['tag'] = "txt_validacao";
		$campos[6]['nome'] = "maiorque";
		$campos[6]['lingua']['pt'] = "O valor do campo {CAMPO} não pode ser menor do que {NUMERO}.";
		$campos[6]['lingua']['en'] = "The field value {CAMPO} can not be less than {NUMERO}. ";
		$campos[6]['lingua']['default'] = "The field value {CAMPO} can not be less than {NUMERO}. ";
		
		//menorque / 
		$campos[7]['tag'] = "txt_validacao";
		$campos[7]['nome'] = "menorque";
		$campos[7]['lingua']['pt'] = "O valor do campo {CAMPO} não pode ser maior do que {NUMERO}.";
		$campos[7]['lingua']['en'] = "The value of the field {CAMPO} can not be larger than {NUMERO}'.";
		$campos[7]['lingua']['default'] = "The value of the field {CAMPO} can not be larger than {NUMERO}'.";
		
		//  / array_um_obrigatorio / 
		$campos[8]['tag'] = "txt_validacao";
		$campos[8]['nome'] = "array_um_obrigatorio";
		$campos[8]['lingua']['pt'] = "Por favor preêncha pelo menos um campo.";
		$campos[8]['lingua']['en'] = "Please fill in at least one field.";
		$campos[8]['lingua']['default'] = "Please fill in at least one field.";
		
		//  valida_nif / 
		$campos[9]['tag'] = "txt_validacao";
		$campos[9]['nome'] = "valida_nif";
		$campos[9]['lingua']['pt'] = "Por favor digite um numero de contribuinte válido";
		$campos[9]['lingua']['en'] = "Please enter a valid taxpayer number.";
		$campos[9]['lingua']['default'] = "Please enter a valid taxpayer number.";
		
		//  valida_nif / 
		$campos[10]['tag'] = "txt_validacao";
		$campos[10]['nome'] = "valida_igual";
		$campos[10]['lingua']['pt'] = "O campo {CAMPO1} e o campo {CAMPO2} têem de ser iguais.";
		$campos[10]['lingua']['en'] = "Please enter a valid taxpayer number.";
		$campos[10]['lingua']['default'] = "Please enter a valid taxpayer number.";
		
		return $campos[$numero]['lingua'][$lingua];

	}
    // txt_validacao / required / O campo {CAMPO} é obrigatório
	function valida_required($campo, $nome_campo) {
		if($campo == ""){
			$erro = $this->devolveConfig(0, "pt");
			$erro = str_replace("{CAMPO}", $nome_campo, $erro);
			return $erro;
		}
		return 0;
    }
	//Tradução txt_validacao / email / O campo {CAMPO} não é um e-mail válido.
    function valida_email($campo, $nome_campo) {
		if (filter_var($campo, FILTER_VALIDATE_EMAIL) == false) {
			$erro = $this->devolveConfig(1, "pt");
			$erro = str_replace("{CAMPO}", $nome_campo, $erro);
			return $erro;
		}
		return 0;
    }
	//Tradução txt_validacao / inteiro / O campo {CAMPO} não é numérico.
    function valida_inteiro($campo, $nome_campo) {
		if (!is_numeric($campo)) {
			$erro = $this->devolveConfig(2, "pt");
			$erro = str_replace("{CAMPO}", $nome_campo, $erro);
			return $erro;
		}
		return 0;
    }
	//Tradução txt_validacao / maxlenght / O campo {CAMPO} tem de ter no máximo {NUMERO} caracteres.
    function valida_maxlenght($campo, $nome_campo, $numero) {
		$contador = strlen($campo);
		if ($contador > $numero){
			$erro = $this->devolveConfig(3, "pt");
			$erro = str_replace("{CAMPO}", $nome_campo, $erro);
			$erro = str_replace("{NUMERO}", $numero, $erro);
			return $erro;
		}
		return 0;
    }
	//Tradução txt_validacao / minlenght / O campo {CAMPO} tem de ter no mínimo {NUMERO} caracteres.
    function valida_minlenght($campo, $nome_campo, $numero) {
		$contador = strlen($campo);
		if ($contador < $numero) {
			$erro = $this->devolveConfig(4, "pt");
			$erro = str_replace("{CAMPO}", $nome_campo, $erro);
			$erro = str_replace("{NUMERO}", $numero, $erro);
			return $erro;
		}
		return 0;
    }
	//Tradução txt_validacao / menorque / O valor do campo {CAMPO} não pode ser maior do que {NUMERO}.
    function valida_menorque($campo, $nome_campo, $numero) {
		$contador = strlen($campo);
		if ($contador > $numero) {
			$erro = $this->devolveConfig(5, "pt");
			$erro = str_replace("{CAMPO}", $nome_campo, $erro);
			$erro = str_replace("{NUMERO}", $numero, $erro);
			return $erro;
		}
		return 0;
    }
	//Tradução txt_validacao / maiorque / O valor do campo {CAMPO} não pode ser menor do que {NUMERO}.
    function valida_maiorque($campo, $nome_campo, $numero) {
		if ($campo < $numero) {
			$erro = $this->devolveConfig(6, "pt");
			$erro = str_replace("{CAMPO}", $nome_campo, $erro);
			$erro = str_replace("{NUMERO}", $numero, $erro);
			return $erro;
		}
		return 0;
    }
	//Tradução txt_validacao / data_mysql / O campo {CAMPO} tem de ter o formato "AAAA-MM-DD".
    function valida_data_mysql($campo, $nome_campo) {
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$campo) == 0){
			$erro = $this->devolveConfig(7, "pt");
			$erro = str_replace("{CAMPO}", $nome_campo, $erro);
			return $erro;
		}
		return 0;
    }
	//Tradução txt_validacao / array_um_obrigatorio / Por favor preêncha pelo menos um campo.
    function valida_array_um_obrigatorio($campos) {
		$campos = array_filter($campos);
		if (empty($campos)){
			$erro = $this->devolveConfig(8, "pt");
			return $erro;
		}
		return 0;
    }
	//Tradução txt_validacao / array_um_obrigatorio / Por favor preêncha pelo menos um campo.
    function valida_nif($campos) {
		if (!$this->contribuinteValido($campos)){
			$erro = $this->devolveConfig(9, "pt");
			return $erro;
		}
		return 0;
    }
	//Tradução txt_validacao / array_um_obrigatorio / Por favor preêncha pelo menos um campo.
    function valida_igual($campo1, $campo2, $nome_campo1, $nome_campo2) {
		if($campo1 != $campo2) {
			$erro = $this->devolveConfig(10, "pt");
			$erro = str_replace("{CAMPO1}", $nome_campo1, $erro);
			$erro = str_replace("{CAMPO2}", $nome_campo2, $erro);
			return $erro;
		}
		return 0;
    }
	
	function contribuinteValido($nif){
		$xx = 0;
	   
		if($nif >= 100000000)
		{
			for($p = 1 ; $p < 9 ; $p++)
			{
				$xx = $xx + substr($nif, $p-1, 1) * ($p + 1);
			}
		   
			$ch_digit = fmod($xx, 11);
		   
			if($ch_digit == 10)
			{
				$ch_digit = 0;
			}
		   
			if(substr($nif, 8, 1) != $ch_digit)
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	   
		return true;
	}

}
	
?>