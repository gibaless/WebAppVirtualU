<?
session_start();

header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
//header("ExpiresAbsolute: " . time() -10); //Expira página Absolutamente
header("Expires: -100000"); //Expira página

include "common/func_getparameter.inc";
// Función la cual reemplaza la comilla simple por la doble comilla simple.
function SafeSql($text){
	return stripslashes(str_replace("'", "''", $text));
}

$MsgBoxMessage = "OK";
	
include "common/inc_database_open.php";


$iId = 0;
if(GetParameter("id") != ""){
	$iId = GetParameter("id");
}
$iPag = 0;
if(GetParameter("p") != ""){
	$iPag = GetParameter("p");
}
$iIdUsuario = 0;
if(GetParameter("idu") != ""){
	$iIdUsuario = GetParameter("idu");
}

// Borro los puntos anteriores
$strSQLComm = " DELETE FROM correccion " .
			  " WHERE entrega_id = " . SafeSql($iId) .
			  " AND correccion_pagina = " . SafeSql($iPag);
mysql_query($strSQLComm);	
// Obtengo si ocurrió algún error
if(mysql_error() != ""){
	$MsgBoxMessage = "ERROR";
}

if($MsgBoxMessage != "ERROR"){
	for($ii = 0; $ii < GetParameter("cant"); $ii++){
	
		$iX = 0;
		if(GetParameter("x" . $ii) != ""){
			$iX = GetParameter("x" . $ii);
		}
		
		$iY = 0;
		if(GetParameter("y" . $ii) != ""){
			$iY = GetParameter("y" . $ii);
		}
		
		$strTipo = "";
		if(GetParameter("tipo" . $ii) != ""){
			$strTipo = GetParameter("tipo" . $ii);
		}
		
		$strColor = "";
		if(GetParameter("color" . $ii) != ""){
			$strColor = GetParameter("color" . $ii);
		}
		
		$strTexto = 0;
		if(GetParameter("texto" . $ii) != ""){
			$strTexto = GetParameter("texto" . $ii);
		}
		
		$strTam = 0;
		if(GetParameter("t" . $ii) != ""){
			$strTam = GetParameter("t" . $ii);
		}
		
		$strNegrita = "";
		if(GetParameter("n" . $ii) != ""){
			$strNegrita = GetParameter("n" . $ii);
		}
		
		$strCursiva = "";
		if(GetParameter("c" . $ii) != ""){
			$strCursiva = GetParameter("c" . $ii);
		}
		
		$strSubrayado = "";
		if(GetParameter("s" . $ii) != ""){
			$strSubrayado = GetParameter("s" . $ii);
		}
		
		
		// Armo la sentencia de INSERT
		$strSQLComm = " INSERT INTO correccion(entrega_id, correccion_pagina, usuario_id, correccion_posicion_x, correccion_posicion_y, correccion_tipo, correccion_color, correccion_texto, correccion_tam, correccion_negrita, correccion_cursiva, correccion_subrayado " .
						" ) " .
						" VALUES (" .
						SafeSql($iId) . "," .
						SafeSql($iPag) . "," .
						SafeSql($iIdUsuario) . "," .
						SafeSql($iX) . "," .
						SafeSql($iY) . "," .
						"'" . SafeSql($strTipo) . "'," .
						"'" . SafeSql($strColor) . "'," .
						"'" . SafeSql($strTexto) . "'," .
						SafeSql($strTam) . "," .
						"'" . SafeSql($strNegrita) . "'," .
						"'" . SafeSql($strCursiva) . "'," .
						"'" . SafeSql($strSubrayado) . "'" .
						")";
		mysql_query($strSQLComm);
		
		// Obtengo si ocurrió algún error
		if(mysql_error() != ""){
			//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
			$MsgBoxMessage = "ERROR";
		}
	}
}

include "common/inc_database_close.php";
echo($MsgBoxMessage);exit();
?>