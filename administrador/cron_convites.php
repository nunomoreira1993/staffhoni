<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";
require_once($_SERVER['DOCUMENT_ROOT'] . '/administrador/pagamentos/pagamentos.obj.php');
$dbpagamentos = new pagamentos($db);
error_reporting(-1);
set_time_limit(0);
$caminho = $_SERVER['DOCUMENT_ROOT'] . "/fotos/convites/";
if ($handle = opendir($caminho)) {

    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
			pr($entry);
            $convite = $dbpagamentos->devolveConviteFicheiro($entry);
            if($convite){
                $data_evento = $convite['data_evento'];
				pr($convite);
                if($data_evento < date('Y-m-d', strtotime(' -2 months'))){
                    unlink($caminho.$entry);
                    pr("<b> Ficheiro apagado </b>:" . $entry.", <i>".$data_evento."</i>");
            
                }
                else {
                    pr("Ficheiro não apagado:" . $entry . ", <i>" . $data_evento . "</i>");

                }
            }
            else{
                pr("Ficheiro não encontrado ".$entry);
                
            }
        }
    }

    closedir($handle);
}
