<?
/**********************************************************************
// ** FUNCTION **
// Nombre: func_pagination.inc
// Versión: 1.2
// Objetivo: Arma la paginación de una consulta.
//----------------------------------------------------------------------
// Autor: Gerardo J. Ruano
// Fecha de creación: 17/02/2004
***********************************************************************/

// Parámetros:
//	- SQLText: Consulta de la que se paginará.
//	- AdditionalParams: Parámetros adicionales que estarán en el QueryString.

// Clases del tipo CSS que pueden implementarse:
//	- PagItem: Style para los nros. que no fueron seleccionados, incluyendo Prev. y Next
//	- PagItemSelected: Style para el Link seleccionado

/***********************************************************************
// NOTA: La función Pagination debe llamarse antes que se haga
//		el Open del RecordSet.
  ***********************************************************************/

// Cantidad de filas por página a mostrar
$PagRowsPerPage=10;
// El AbsolutePosition del RecordSet se debe setear luego del Open del mismo (SIEMPRE MAYOR A CERO),
// Si es 0 indica que la consulta tiene 0 registros y da un ERROR
$PagAbsolutePosition=0;
// Registro por el cual se comienza a Loopear la consulta
$PagCurrentRow=0;
// Cantidad de páginas máxima que se quiere mostrar (SIEMPRE MAYOR A CERO)
$PagMaxPages=-1;

function Pagination( $SQLText, $AdditionalParams )
{
	global $PagRowsPerPage;
	global $PagAbsolutePosition;
	global $PagCurrentRow;
	global $PagMaxPages;

    $iNumPerPage = 0;
    $iTtlNumItems = 0;
    $iDBLoc = 0;
    $sSqlTemp = "";
    $iTtlTemp = 0;
    $iDBLocTemp = 0;
    $sURLBeg = "";
    $iA = 0;
    $iB = 0;
    $x = 0;
    $iTemp = 0;
    
	if ( GetParameter("rowsperpage") != "" )
	{
		$iNumPerPage = (int)GetParameter("rowsperpage");
	}
	else 
	{
		$iNumPerPage = $PagRowsPerPage;
	}
	
	// Seteo de la cantidad de filas por página
    $PagRowsPerPage = $iNumPerPage;
    // Seteo del la posición absoluta del RecordSet
    ////$PagAbsolutePosition = GetParameter("iDBLoc") + 1;
    $PagAbsolutePosition = GetParameter("iDBLoc");
    if ( $PagAbsolutePosition == "" )
		$PagAbsolutePosition = 0;
    
    $iDBLoc = GetParameter("iDBLoc");
    $iTtlNumItems = GetParameter("ittlnumitems");
    // Get ttl num of items from the database if its Not already In the QueryString
    if ($iTtlNumItems == "")
    {
		$iPosFROM = 0;
    	
		$Result = mysql_query($SQLText);
    	
    	$iTtlNumItems = mysql_num_rows($Result);
    	
    	mysql_free_result($Result);
    }
    $iTtlTemp = (int)($iTtlNumItems / $iNumPerPage); // this is the number of numbers overall (use the "\" To return int)
    $iDBLocTemp = (int)($iDBLoc / $iNumPerPage);	// this is which number we are currently On (use the "\" To return int)
    if ($AdditionalParams != "") 
    {
    	$sURLBeg = "<a class='paginador' href='" . $_SERVER["SCRIPT_NAME"] . "?" . $AdditionalParams . "&rowsperpage=" . $iNumPerPage . "&ittlnumitems=" .  $iTtlNumItems . "&iDBLoc=";
    }
    else
    {
    	$sURLBeg = "<a class='paginador' href='" . $_SERVER["SCRIPT_NAME"] . "?ittlnumitems=" . $iTtlNumItems . "&rowsperpage=" . $iNumPerPage . "&iDBLoc=";
    }
    
	$strResultPagination = "";
	
    //***** BEGIN DISPLAY *****//
    // Print the "previous"
    if ($iDBLoc != 0) 
    {
    	$strResultPagination = $strResultPagination . $sURLBeg . ($iDBLoc - $iNumPerPage) . "' class='PagItem'>Prev.</a>  ";
    }
    
    // Print the <<
    if ($iDBLocTemp >= $iNumPerPage) 
    {
		/*
		$dVal = (( (int)($iDBLocTemp / $iNumPerPage) ) * $iNumPerPage ^ 2) - ($iNumPerPage * 9);
		$strResultPagination = $strResultPagination . $sURLBeg . $dVal . "'><<</a> ";
		*/
    }
    
    // Print the numbers in between. Print them out in sets of 10.
    $iA = ( (int)($iDBLocTemp / $iNumPerPage) ) * $iNumPerPage;

	// Seteo la cantidad de páginas a mostrar    
    if ( $PagMaxPages > 0 && $PagMaxPages > $iA ) 
    	$iB = $PagMaxPages - 1;
	else
		$iB = ( (int)($iDBLocTemp / $iNumPerPage) ) * $iNumPerPage + $iNumPerPage;
    
    $i = 0;
    for ($x=$iA; $x <= $iB; $x++)
    {
    	$iTemp = ($x * $iNumPerPage);
    	if ($iTemp < $iTtlNumItems) // takes care of extra numbers after the overall final number
    	{
    		$iProxValor = $x + 1;
    		if ($iDBLoc == $iTemp)
    		{
    			$strResultPagination = $strResultPagination . " <b class='PagItemSelected'>[" . $iProxValor . "]</b>";
    		}
    		else
    		{
    			$strResultPagination = $strResultPagination .  " " . $sURLBeg . ($x * $iNumPerPage) . "' class='PagItem'>" . $iProxValor . "</a>";
    		}
    	}
    	else
    		break;
    }
    
    // Print the >>
    if ($iTtlTemp > $iDBLocTemp) 
    {
    	if (($iDBLocTemp + $iNumPerPage) <= $iTtlTemp)
    	{
    		// $strResultPagination = $strResultPagination .  " " . $sURLBeg . (( (int)($iDBLocTemp / $iNumPerPage) ) * $iNumPerPage + $iNumPerPage ) * $iNumPerPage . "' class='PagItem'>>></A> ";
    	}
    }
    // Print the "Next"
    if (($iDBLoc + $iNumPerPage) < $iTtlNumItems)
    {
    	$strResultPagination = $strResultPagination . "  " . $sURLBeg . ($iDBLoc + $iNumPerPage) . "' class='PagItem'>Next</A>";
    }
    //***** End DISPLAY *****//
    
    if ( $iTtlNumItems == 0 )
		$PagAbsolutePosition = 0;

	return $strResultPagination;
}
?>