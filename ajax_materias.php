<?
session_start();

/* INCLUYO ARCHIVO DE CONFIGURACIÓN. */
include "includes/sitesettings_inc.php";
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "common/func_getparameter.inc";

header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
//header("ExpiresAbsolute: " . time() -10); //Expira página Absolutamente
header("Expires: -100000"); //Expira página

$iIdMateria = "";
if(GetParameter("id") != "" && is_numeric(GetParameter("id"))){
	$iIdMateria = GetParameter("id");
}

$iIdCarrera = "";
if(GetParameter("idr") != "" && is_numeric(GetParameter("idr"))){
	$iIdCarrera = GetParameter("idr");
}

$iIdUsuario = "";
if(GetParameter("u") == "Y"){
	if(isset($_SESSION["UserId"]) && $_SESSION["UserId"] !=""){
		$iIdUsuario = $_SESSION["UserId"];
	}
}

$strDefecto = "--Todas--";
if(GetParameter("d") != ""){
	$strDefecto = GetParameter("d");
}

$strClientScript = "";
if(GetParameter("s") == "Y"){
	$strClientScript = "onchange=\"TraerComisiones('','Y','" . $strDefecto . "','" . GetParameter("u") . "');\"";
}

include "common/inc_database_open.php";
ObtenerComboMateria($iIdUsuario, $iIdMateria, $iIdCarrera, $strClientScript, $strDefecto, "N");
include "common/inc_database_close.php";
?>

