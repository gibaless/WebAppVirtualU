<?
session_start();

/*
header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
header("Expires: -100000"); //Expira página
header('Content-Type: text/xml');//le digo al browser que es un documento xml
*/
include "common/func_getparameter.inc";

$ErrorNumber = "0";
$ErrorDescription = "";

$iId = 0;
if(GetParameter("id") != "" && is_numeric(GetParameter("id"))){
	$iId = GetParameter("id");
}
$iPagina = -1;
if(GetParameter("p") != "" && is_numeric(GetParameter("p"))){
	$iPagina = GetParameter("p");
}


echo '<?xml version="1.0" encoding="iso-8859-1"?>'; 
echo '<contenidos>';

include "common/inc_database_open.php";
$strSql = "SELECT C.correccion_posicion_x, C.correccion_posicion_y, C.correccion_tipo, C.correccion_color, C.correccion_texto, " . 
		  " C.correccion_tam, C.correccion_negrita, C.correccion_cursiva, C.correccion_subrayado " .
		  " FROM correccion AS C " . 
		  " WHERE entrega_id = " . $iId . 
		  " AND correccion_pagina = " . $iPagina;
$Result = mysql_query($strSql);
if(mysql_num_rows($Result)){
	while ( $oRs = mysql_fetch_array($Result) )
	{
		echo '<contenido>' .
					'<x>' . $oRs["correccion_posicion_x"] . '</x>' .
					'<y>' . $oRs["correccion_posicion_y"] . '</y>' .
					'<tipo>' . $oRs["correccion_tipo"] . '</tipo>' .
					'<color>' . $oRs["correccion_color"] . '</color>' .
					'<texto>' . $oRs["correccion_texto"] . '</texto>' .
					'<t>' . $oRs["correccion_tam"] . '</t>' .
					'<n>' . $oRs["correccion_negrita"] . '</n>' .
					'<c>' . $oRs["correccion_cursiva"] . '</c>' .
					'<s>' . $oRs["correccion_subrayado"] . '</s>' .
			 '</contenido>';
	}
}else{
	$ErrorNumber = "-1";
	$ErrorDescription = "Error al traer la información.";
}
mysql_free_result($Result);
echo '</contenidos>';
echo '<error>' .
		'<codigo>' . $ErrorNumber . '</codigo>' .
		'<descripcion>' . $ErrorDescription . '</descripcion>' .
	'</error>';
include "common/inc_database_close.php";
?>