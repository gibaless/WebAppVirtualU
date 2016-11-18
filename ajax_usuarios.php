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


$iIdUser= "";
if(GetParameter("id") != "" && is_numeric(GetParameter("id"))){
	$iIdUser = GetParameter("id");
}

$iIdGrupo = "";
if(GetParameter("idg") != "" && is_numeric(GetParameter("idg"))){
	$iIdGrupo = GetParameter("idg");
}

$iIdUsuario = "";
if(GetParameter("u") == "Y"){
	if(isset($_SESSION["UserId"]) && $_SESSION["UserId"] !=""){
		$iIdUsuario = $_SESSION["UserId"];
	}
}

$strDefecto = "--Todos--";
if(GetParameter("d") != ""){
	$strDefecto = GetParameter("d");
}

$strClientScript = "";
if(GetParameter("s") == "Y"){
	$strClientScript = "";
}

include "common/inc_database_open.php";
ObtenerComboUsuario($iIdUsuario, $iIdUser, $iIdGrupo, $strClientScript, $strDefecto, "N");
include "common/inc_database_close.php";
?>

