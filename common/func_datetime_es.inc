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
	$arrFecha = explode(" ", $strFecha);
	$strFecha = $arrFecha[0];
	$arrDate = explode("/", $strFecha);
	$intDay = $arrDate[0];
	$intMonth = $arrDate[1];
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
		$ObjDate = strftime("%d/%m/%Y", strtotime($ObjDate));
		
		if(isDate($ObjDate))
			return $ObjDate;
	}
}

// OBJETIVO: Formatear una fecha como: dd/mm/yyyy HH:mm:ss
function DateTimeFormat($ObjDate)
{
	if($ObjDate != "")
	{
		$ObjDate = strftime("%d/%m/%Y %H:%M:%S", strtotime($ObjDate));
		
		if(isDate($ObjDate))
			return $ObjDate;
	}
}


// OBJETIVO: Recibe una fecha que viene en formato dd/mm/yyyy y la convierte a un objeto DATE
function String2Date($strDate)
{
	$arrFecha = explode(" ", $strDate);
	$arrFecha = $arrFecha[0];
	$arrFecha = explode("/", $arrFecha);
	$ObjFecha = $arrFecha[0] . "/" . $arrFecha[1] . "/" . $arrFecha[2];
	
	if(isDate($ObjFecha)){
		return $ObjFecha;
	}
}

// OBJETIVO: Recibe una fecha que viene en formato dd/mm/yyyy y la convierte a un objeto DATE
function String2DateTime($strDateTime)
{
	$arrFecha = explode(" ", $strDateTime);
	$arrDate = $arrFecha[0];
	$strTime = $arrFecha[1];
	$arrFecha = explode("/", $arrDate);
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
		$arrFecha = explode("/", $ObjFecha);
		
		// Anio.
		$strFecha = $arrFecha[2] . "-";
		
		// Mes
		if($arrFecha[1] < 10 && !ereg('0', $arrFecha[1]))
			$strFecha = $strFecha . "0" . $arrFecha[1] . "-";
		else
			$strFecha = $strFecha . $arrFecha[1] . "-";
		
		// Dia
		if($arrFecha[0] < 10 && !ereg('0', $arrFecha[0]))
			$strFecha = $strFecha . "0" . $arrFecha[0];
		else
			$strFecha = $strFecha . $arrFecha[0];
		
		
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
		$arrFecha = explode(" ", $ObjFecha);
		$strDate = $arrFecha[0];
		$strTime = $arrFecha[1];
		$arrFecha = explode("/", $strDate);
		
		// Anio.
		$strFecha = $arrFecha[2] . "-";
		
		// Mes
		if($arrFecha[1] < 10 && !ereg('0', $arrFecha[1]))
			$strFecha = $strFecha . "0" . $arrFecha[1] . "-";
		else
			$strFecha = $strFecha . $arrFecha[1] . "-";
		
		// Dia
		if($arrFecha[0] < 10 && !ereg('0', $arrFecha[0]))
			$strFecha = $strFecha . "0" . $arrFecha[0];
		else
			$strFecha = $strFecha . $arrFecha[0];


		$SQLDateTime = "'" . $strFecha . " " . $strTime . "'";  //ESTO ES PARA MYSQL.
		
		return $SQLDateTime;
	}else
		return "null";
}

//Formatea la fecha a Lunes 28 de Diciembre de 2009
function formatear_fecha($Fecha){
	$strResult = "";
	$strMes = "";
	$FechaAux =  strftime("%Y/%m/%d", strtotime($Fecha));
	$d = strftime("%w",strtotime($FechaAux));
	//Obtengo el nombre del día de la semana
	switch($d){
		case 0:
			$strResult = "Domingo";
			break;
		case 1:
			$strResult = "Lunes";
			break;
		case 2:
			$strResult = "Martes";
			break;
		case 3:
			$strResult = "Miércoles";
			break;
		case 4:
			$strResult = "Jueves";
			break;
		case 5:
			$strResult = "Viernes";
			break;
		case 6:
			$strResult = "Sábado";
			break;
		
	}
	
	//Obtengo el nombre del mes
	switch(strftime("%m", strtotime($Fecha))){
		case 1:
			$strMes = "Enero";
			break;
		case 2:
			$strMes = "Febrero";
			break;
		case 3:
			$strMes = "Marzo";
			break;
		case 4:
			$strMes = "Abril";
			break;
		case 5:
			$strMes = "Mayo";
			break;
		case 6:
			$strMes = "Junio";
			break;
		case 7:
			$strMes = "Julio";
			break;
		case 8:
			$strMes = "Agosto";
			break;
		case 9:
			$strMes = "Septiembre";
			break;
		case 10:
			$strMes = "Octubre";
			break;
		case 11:
			$strMes = "Noviembre";
			break;
		case 12:
			$strMes = "Diciembre";
			break;
	}
	
	$strResult = $strResult . " " . strftime("%d", strtotime($Fecha)) . " de " . $strMes . " de " . strftime("%Y", strtotime($Fecha));
	return $strResult;
}

// Retorna el nombre del mes
function ObtenerNombreMes($iMes)
{
	$strResult = "";

	// Obtengo el nombre del mes
	switch($iMes)
	{
		case 1:
			$strResult = "Enero";
			break;
		case 2:
			$strResult = "Febrero";
			break;
		case 3:
			$strResult = "Marzo";
			break;
		case 4:
			$strResult = "Abril";
			break;
		case 5:
			$strResult = "Mayo";
			break;
		case 6:
			$strResult = "Junio";
			break;
		case 7:
			$strResult = "Julio";
			break;
		case 8:
			$strResult = "Agosto";
			break;
		case 9:
			$strResult = "Septiembre";
			break;
		case 10:
			$strResult = "Octubre";
			break;
		case 11:
			$strResult = "Noviembre";
			break;
		case 12:
			$strResult = "Diciembre";
			break;
	}
	
	return $strResult;
}

function ultimoDia($mes,$ano)
{
    static $ultimo_dia;
	
	$ultimo_dia=28;
    while (checkdate($mes,$ultimo_dia + 1,$ano))
	{
       $ultimo_dia++;
    } 
	
    return $ultimo_dia;
} 

/*
FUNCIONA LA CUAL SUMA/RESTA DIAS A UNA FECHA
// echo sumaDia('30/01/2008',5); 	==> salida: 04/02/2008
// echo sumaDia('30/01/2008',-5); 	==> salida: 25/01/2008
*/
function sumaDia($fecha, $dia, $formato = 'd/m/Y')
{
	list($day,$mon,$year) = explode('/',$fecha);
	return date($formato,mktime(0,0,0,$mon,$day+$dia,$year));		
}

/*
FUNCIONA LA CUAL SUMA/RESTA MESES A UNA FECHA
// echo sumaMes('30/04/2008',1); 	==> salida: 30/05/2008
// echo sumaMes('30/04/2008',-1); 	==> salida: 30/03/2008
*/
function sumaMes($fecha, $mes, $formato = 'd/m/Y')
{
	list($day,$mon,$year) = explode('/',$fecha);
	return date($formato,mktime(0,0,0,$mon+$mes,$day,$year));
}
?>