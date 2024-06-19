<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/config.php";

setlocale(LC_TIME, 'pt_PT.utf-8');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
date_default_timezone_set("Europe/Lisbon");

if (session_id() == '') {
  session_start();
}

if($_GET["rp"] && $_GET["id"]) {
  require_once($_SERVER['DOCUMENT_ROOT'] . '/rp/rp.obj.php');
  $dbrp = new rp($db, $_GET["id"]);
  $rp = $dbrp->devolveInfo();

  if(empty($rp["qrcode"])){
    $qrcode_hash = md5(strtotime("now") . $rp["id"]);
    $db->update("rps", array("qrcode"=> $qrcode_hash), "id = " . $rp["id"]);
    include_once($_SERVER["DOCUMENT_ROOT"] . '/plugins/phpqrcode/lib/full/qrlib.php');
    $qrcode =  "/fotos/convites/" . "honi_qrcode_" . $rp["id"].".png";
    QRcode::png($qrcode_hash, $_SERVER["DOCUMENT_ROOT"] . $qrcode, QR_ECLEVEL_L, 35, 2);
    $rp = $dbrp->devolveInfo();
  }
  else {
    $qrcode =  "/fotos/convites/" . "honi_qrcode_" . $rp["id"].".png";
  }

  if ($rp["qrcode"] && file_exists($_SERVER["DOCUMENT_ROOT"] . $qrcode)) {
      header('Content-Description: File Transfer');
      header('Content-Type: image/png');
      header('Content-Disposition: attachment; filename="honi_qrcode_' . $rp["id"]. '.png"');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Expires: 0');

      echo file_get_contents($_SERVER["DOCUMENT_ROOT"] . $qrcode);
      exit;
  }
  else {
      echo "<h1>Não é possivel efetuar download.</h1>";
      echo "<h3>QR CODE não foi gerado corretamente.</h3>";
      die;
  }
}
else {
    echo "<h1>Não é possivel efetuar download.</h1>";
    echo "<h3>RP Não encontrado.</h3>";
    die;
}