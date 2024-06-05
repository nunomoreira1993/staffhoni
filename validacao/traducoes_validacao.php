<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/config.obj.php");
require_once($dir_site . "/plugins/validacao/validacao.obj.php");
$dbvalidacao = new validacao($db);	
global $lingua, $db;
?>
<h2> Inserir traduções - Validações </h2>
<hr/>
<?php 
$campos = $dbvalidacao -> devolveConfig();
//INSERE TRADUCAO
//grava tabelas
$tabela_traducoes = "traducoes";
$tabela_traducoes_campos = "traducoes_campos";

foreach($campos as $campo){
	$valor_traducoes['tag'] = $campo['tag'];
	$valor_traducoes['nome'] = $campo['nome'];
	$id_traducao = $dbvalidacao -> existeTraducao($valor_traducoes['tag'], $valor_traducoes['nome']);
	if($id_traducao == 0){
		$id_insere = $db->Insere($tabela_traducoes, $valor_traducoes);
	}else{
		$id_insere = $id_traducao;
	}
	foreach($linguas as $ling){
		if(empty($campo['lingua'][$ling['sigla']])){
			$valor_traducoes_campos['valor'] = $campo['lingua']['default'];
		}else{
			$valor_traducoes_campos['valor'] = $campo['lingua'][$ling['sigla']];
		}
		$valor_traducoes_campos['lingua'] = $ling['sigla'];
		$valor_traducoes_campos['id'] = $id_insere;
		if($id_traducao != 0){
			$db->Altera($tabela_traducoes_campos, $valor_traducoes_campos, "id=".$id_traducao);
		}else{
			$db->Insere($tabela_traducoes_campos, $valor_traducoes_campos);
		}
	}
}
?>
<h2> Concluído </h2>

<?php 
header('Location: /adm/seara/translator_generator.php');

 