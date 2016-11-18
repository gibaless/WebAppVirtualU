<?
session_start();

header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
//header("ExpiresAbsolute: " . time() -10); //Expira página Absolutamente
header("Expires: -100000"); //Expira página

/* INCLUYO ARCHIVO DE CONFIGURACIÓN. */
include "includes/sitesettings_inc.php";
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "common/func_getparameter.inc";?>
<option value="">--Seleccionar--</option>
<?

$iIdProvincia = 0;

if(GetParameter("id") != "" && is_numeric(GetParameter("id"))){
	$iIdProvincia = GetParameter("id");
}

if(GetParameter("idp") != "" && is_numeric(GetParameter("idp"))){
	include "common/inc_database_open.php";
	$strSQLComm =	" SELECT prov_id, estado " .
					" FROM provincias_estados " .
					" WHERE pais_id = " . SafeSql(GetParameter("idp"));
	
	$Result = mysql_query($strSQLComm);

	while ( $ObjRs = mysql_fetch_array($Result) )
	{
		?><option value="<?=$ObjRs["prov_id"]?>" <? if(trim("".$iIdProvincia) == trim("".$ObjRs["prov_id"])){?> selected="selected"<? }?>><?=$ObjRs["estado"]?></option><?
	}
	mysql_free_result($Result);
	include "common/inc_database_close.php";
}
?>


