<?
/**********************************************************************
// ** FUNCTION **
// Nombre: func_pagination.inc
//----------------------------------------------------------------------
// Autor: Lucas F. Ruano
// Fecha de creación: 01/12/2007
***********************************************************************/
// Valida una fecha.
function isDate($strFecha)
{
	$arrFecha = split(" ", $strFecha);
	$strFecha = $arrFecha[0];
	$arrDate = explode("/", $strFecha);
	$intDay = $arrDate[1];
	$intMonth = $arrDate[0];
	$intYear = $arrDate[2];
	
	if(checkdate($intMonth, $intDay, $intYear)){
		return true;
	}else{
		return false;
	}
}

// OBJETIVO: Formatear una fecha como: dd/mm/yyyy
function DateFormat($ObjDate)
{
	if($ObjDate != "")
	{
		$ObjDate = strftime("%m/%d/%Y", strtotime($ObjDate));
		
		if(isDate($ObjDate))
			return $ObjDate;
	}
}

// OBJETIVO: Formatear una fecha como: dd/mm/yyyy HH:mm:ss
function DateTimeFormat($ObjDate)
{
	if($ObjDate != "")
	{
		$ObjDate = strftime("%m/%d/%Y %H:%M:%S", strtotime($ObjDate));
		
		if(isDate($ObjDate))
			return $ObjDate;
	}
}


// OBJETIVO: Recibe una fecha que viene en formato dd/mm/yyyy y la convierte a un objeto DATE
function String2Date($strDate)
{
	$arrFecha = split(" ", $strDate);
	$arrFecha = $arrFecha[0];
	$arrFecha = split("/", $arrFecha);
	$ObjFecha = $arrFecha[0] . "/" . $arrFecha[1] . "/" . $arrFecha[2];
	
	if(isDate($ObjFecha)){
		return $ObjFecha;
	}
}

// OBJETIVO: Recibe una fecha que viene en formato dd/mm/yyyy y la convierte a un objeto DATE
function String2DateTime($strDateTime)
{
	$arrFecha = split(" ", $strDateTime);
	$arrDate = $arrFecha[0];
	$strTime = $arrFecha[1];
	$arrFecha = split("/", $arrDate);
	$ObjFecha = $arrFecha[0] . "/" . $arrFecha[1] . "/" . $arrFecha[2];
	
	if(isDate($ObjFecha)){
		return $ObjFecha . " " . $strTime;
	}
}


// OBJETIVO: Validar una fecha que viene en formato dd/mm/yyyy
function SQLDate($strDate)
{
	$ObjFecha = String2Date($strDate);
	
	if($ObjFecha != "")
	{
		$arrFecha = split("/", $ObjFecha);
		
		// Anio.
		$strFecha = $arrFecha[2] . "-";
		
		// Mes
		if($arrFecha[0] < 10 && !ereg('0', $arrFecha[0]))
			$strFecha = $strFecha . "0" . $arrFecha[0] . "-";
		else
			$strFecha = $strFecha . $arrFecha[0] . "-";
		
		// Dia
		if($arrFecha[1] < 10 && !ereg('0', $arrFecha[1]))
			$strFecha = $strFecha . "0" . $arrFecha[1];
		else
			$strFecha = $strFecha . $arrFecha[1];
		
		
		/*
		'SQLDate = "#" & strFecha & "#"  'ESTO ES PARA ACCESS
		'SQLDate = "CONVERT( DATETIME, '" + strFecha + "', 120  )" 'ESTO ES PARA SQL SERVER
		'SQLDate = "'" & strFecha & "'"  'ESTO ES PARA ACCESS
		*/
		
		$SQLDate = "'" . $strFecha . "'";  //ESTO ES PARA MYSQL.
		
		return $SQLDate;
	}else
		return "null";
}

// OBJETIVO: Validar una fecha que viene en formato dd/mm/yyyy HH:MM:SS
function SQLDateTime($strDateTime)
{
	$ObjFecha = String2DateTime($strDateTime);
	
	if($ObjFecha != "")
	{
		$arrFecha = split(" ", $ObjFecha);
		$strDate = $arrFecha[0];
		$strTime = $arrFecha[1];
		$arrFecha = split("/", $strDate);
		
		// Anio.
		$strFecha = $arrFecha[2] . "-";
		
		// Mes
		if($arrFecha[0] < 10 && !ereg('0', $arrFecha[0]))
			$strFecha = $strFecha . "0" . $arrFecha[0] . "-";
		else
			$strFecha = $strFecha . $arrFecha[0] . "-";
		
		// Dia
		if($arrFecha[1] < 10 && !ereg('0', $arrFecha[1]))
			$strFecha = $strFecha . "0" . $arrFecha[1];
		else
			$strFecha = $strFecha . $arrFecha[1];


		$SQLDateTime = "'" . $strFecha . " " . $strTime . "'";  //ESTO ES PARA MYSQL.
		
		return $SQLDateTime;
	}else
		return "null";
}



/************************************** ESTO POR EL MOMENTO NO VA.
// Valida si una fecha es válida.
function isDate($date) 
{ 
	list($d, $m, $y) = split('[/.-]', $date);	
	$dummy = date("d/m/Y", mktime(0,0,0,$m,$d,$y));
	$date = ereg_replace('-', '/', $date); 
	
	return true;
}

// OBJETIVO: Formatear una fecha como: dd/mm/yyyy
function DateFormat($fecha)
{
	ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha); 
	$lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1]; 

	if(isDate($lafecha))
		return $lafecha;
}

// OBJETIVO: Formatear una fecha para la base de datos.
function SQLDate($fecha)
{
	if(isDate($fecha))
	{
		ereg( "([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $mifecha); 
		$lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1]; 
	
		return $lafecha;
	}else
		return "null";
}

*/
?>