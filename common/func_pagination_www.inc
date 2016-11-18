<?
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

// Registro por el cual se comienza a Loopear la consulta para distintas paginaciones en una misma pagina por ej: disciplinas.php
$PagCurrentRow=0;
// Cantidad de páginas máxima que se quiere mostrar (SIEMPRE MAYOR A CERO)
$PagMaxPages=-1;

function PaginationImg( $SQLText, $AdditionalParams )
{
	global $PagRowsPerPage;
	global $PagAbsolutePosition;
	global $PagAbsolutePosition2;
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
    
	
	$PrepParams = "";
	$sURLBeg = "";
	$Params = "ittlnumitems=" . $iTtlNumItems . "&rowsperpage=" . $iNumPerPage . "&iDBLoc=";
	$sURLBeg = "<a  class='paginador' href='" . $_SERVER["SCRIPT_NAME"] . "?" . $Params;

	if ($AdditionalParams != "") 
    	$PrepParams = "||@Params";
    
	
	
    //***** BEGIN DISPLAY *****//
    
	// Print the numbers in between. Print them out in sets of 10.
    $iA = ( (int)($iDBLocTemp / $iNumPerPage) ) * $iNumPerPage;

	// Seteo la cantidad de páginas a mostrar    
    if ( $PagMaxPages > 0 && $PagMaxPages > $iA ) 
    	$iB = $PagMaxPages - 1;
	else
		$iB = ( (int)($iDBLocTemp / $iNumPerPage) ) * $iNumPerPage + $iNumPerPage;
    
    
	
	
	$i = 0;
	$iCantItems = 0;
    for ($x=$iA; $x <= $iB; $x++)
    {
    	$iTemp = ($x * $iNumPerPage);
    	if ($iTemp < $iTtlNumItems) // takes care of extra numbers after the overall final number
    	{
			$iCantItems++;
    	}
    	else
    		break;
    }
	
	/*
	$iCantItems = 1;
	if($iTtlNumItems > $iNumPerPage)
		$iCantItems = round($iTtlNumItems - $iNumPerPage);
	*/
	
	
	$strResultPagination = "<table cellpadding='0' cellspacing='0'><tr><td>";
	
	// Print the "previous"
    if ($iDBLoc != 0) 
    {
    	$strResultPagination = $strResultPagination . $sURLBeg . ($iDBLoc - $iNumPerPage) . $PrepParams . "')\"> < Ant. </a>  ";
    }else{
		$strResultPagination = $strResultPagination . "";
	}
		
	$strResultPagination =  $strResultPagination . '</td><td>';
	
	$i = 0;
    for ($x=$iA; $x <= $iB; $x++)
    {
    	$iTemp = ($x * $iNumPerPage);
    	if ($iTemp < $iTtlNumItems) // takes care of extra numbers after the overall final number
    	{
			
			
    		$iProxValor = $x + 1;
    		if ($iDBLoc == $iTemp)
    		{
    			$strResultPagination = $strResultPagination . " " . $iProxValor;
    		}
    		else
    		{
    			$strResultPagination = $strResultPagination .  " " . $sURLBeg . ($x * $iNumPerPage) . $PrepParams . "')\">" . $iProxValor . "</a>";
    		}


		}
    	else
    		break;
    }
	
	
	$strResultPagination =  $strResultPagination . '</td><td>';

    // Print the "Next"
    if (($iDBLoc + $iNumPerPage) < $iTtlNumItems)
    {
    	$strResultPagination = $strResultPagination . "  " . $sURLBeg . ($iDBLoc + $iNumPerPage) . $PrepParams . "')\"> Sig. > </a>";
    }else{
		$strResultPagination = $strResultPagination . "";
    }

	$strResultPagination =  $strResultPagination . '</td><tr></table>';
    
    // Print the <<
    if ($iDBLocTemp >= $iNumPerPage) 
    {
		/*
		$dVal = (( (int)($iDBLocTemp / $iNumPerPage) ) * $iNumPerPage ^ 2) - ($iNumPerPage * 9);
		$strResultPagination = $strResultPagination . $sURLBeg . $dVal . "'><<</a> ";
		*/
    }
    
    
    
    // Print the >>
    if ($iTtlTemp > $iDBLocTemp) 
    {
    	if (($iDBLocTemp + $iNumPerPage) <= $iTtlTemp)
    	{
    		// $strResultPagination = $strResultPagination .  " " . $sURLBeg . (( (int)($iDBLocTemp / $iNumPerPage) ) * $iNumPerPage + $iNumPerPage ) * $iNumPerPage . "' class='paginador'>>></A> ";
    	}
    }
    
	
	if($PrepParams != "")
		$strResultPagination = str_replace($PrepParams, '&' . $AdditionalParams, $strResultPagination);
	//***** End DISPLAY *****//
    
    if ( $iTtlNumItems == 0 )
		$PagAbsolutePosition = 0;

	
	//echo $strResultPagination;exit();

	return $strResultPagination;
}


// Cantidad de filas por página a mostrar
$PagRowsPerPage2=10;
// El AbsolutePosition del RecordSet se debe setear luego del Open del mismo (SIEMPRE MAYOR A CERO),
// Si es 0 indica que la consulta tiene 0 registros y da un ERROR
$PagAbsolutePosition2=0;
// Registro por el cual se comienza a Loopear la consulta

// Registro por el cual se comienza a Loopear la consulta para distintas paginaciones en una misma pagina por ej: disciplinas.php
$PagCurrentRow2=0;
// Cantidad de páginas máxima que se quiere mostrar (SIEMPRE MAYOR A CERO)
$PagMaxPages2=-1;

function PaginationImg2( $SQLText2, $AdditionalParams2 )
{
	global $PagRowsPerPage2;
	global $PagAbsolutePosition2;
	global $PagCurrentRow2;
	global $PagMaxPages2;

    $iNumPerPage2 = 0;
    $iTtlNumItems2 = 0;
    $iDBLoc2 = 0;
    $sSqlTemp2 = "";
    $iTtlTemp2 = 0;
    $iDBLocTemp2 = 0;
    $sURLBeg2 = "";
    $iA2 = 0;
    $iB2 = 0;
    $x2 = 0;
    $iTemp2 = 0;
    
	if ( GetParameter("rowsperpage2") != "" )
	{
		$iNumPerPage2 = (int)GetParameter("rowsperpage2");
	}
	else 
	{
		$iNumPerPage2 = $PagRowsPerPage2;
	}
	
	// Seteo de la cantidad de filas por página
    $PagRowsPerPage2 = $iNumPerPage2;
    // Seteo del la posición absoluta del RecordSet
    ////$PagAbsolutePosition = GetParameter("iDBLoc2") + 1;
    $PagAbsolutePosition2 = GetParameter("iDBLoc2");
    if ( $PagAbsolutePosition2 == "" )
		$PagAbsolutePosition2 = 0;
    
    $iDBLoc2 = GetParameter("iDBLoc2");
    $iTtlNumItems2 = GetParameter("ittlnumitems2");
    // Get ttl num of items from the database if its Not already In the QueryString
    if ($iTtlNumItems2 == "")
    {
		$iPosFROM2 = 0;
    	
		$Result2 = mysql_query($SQLText2);
    	
    	$iTtlNumItems2 = mysql_num_rows($Result2);
    	
    	mysql_free_result($Result2);
    }
    $iTtlTemp2 = (int)($iTtlNumItems2 / $iNumPerPage2); // this is the number of numbers overall (use the "\" To return int)
    $iDBLocTemp2 = (int)($iDBLoc2 / $iNumPerPage2);	// this is which number we are currently On (use the "\" To return int)
    
	
	$PrepParams2 = "";
	$sURLBeg2 = "";
	$Params2 = "ittlnumitems2=" . $iTtlNumItems2 . "&rowsperpage2=" . $iNumPerPage2 . "&iDBLoc2=";
	$sURLBeg2 = "<a  class='paginador' href='" . $_SERVER["SCRIPT_NAME"] . "?" . $Params2;

	if ($AdditionalParams2 != "") 
    	$PrepParams2 = "||@Params";
    
	
	
    //***** BEGIN DISPLAY *****//
    
	// Print the numbers in between. Print them out in sets of 10.
    $iA2 = ( (int)($iDBLocTemp2 / $iNumPerPage2) ) * $iNumPerPage2;

	// Seteo la cantidad de páginas a mostrar    
    if ( $PagMaxPages2 > 0 && $PagMaxPages2 > $iA2 ) 
    	$iB2 = $PagMaxPages2 - 1;
	else
		$iB2 = ( (int)($iDBLocTemp2 / $iNumPerPage2) ) * $iNumPerPage2 + $iNumPerPage2;
    
    
	
	
	$i2 = 0;
	$iCantItems2 = 0;
    for ($x2=$iA2; $x2 <= $iB2; $x2++)
    {
    	$iTemp2 = ($x2 * $iNumPerPage2);
    	if ($iTemp2 < $iTtlNumItems2) // takes care of extra numbers after the overall final number
    	{
			$iCantItems2++;
    	}
    	else
    		break;
    }
	
	/*
	$iCantItems2 = 1;
	if($iTtlNumItems2 > $iNumPerPage2)
		$iCantItems2 = round($iTtlNumItems2 - $iNumPerPage2);
	*/
	
	
	$strResultPagination2 = "<table cellpadding='0' cellspacing='0'><tr><td>";
	
	// Print the "previous"
    if ($iDBLoc2 != 0) 
    {
    	$strResultPagination2 = $strResultPagination2 . $sURLBeg2 . ($iDBLoc2 - $iNumPerPage2) . $PrepParams2 . "')\"> << </a>  ";
    }else{
		$strResultPagination2 = $strResultPagination2 . "";
	}
		
	$strResultPagination2 =  $strResultPagination2 . '</td><td>';
	
	$i2 = 0;
    for ($x2=$iA2; $x2 <= $iB2; $x2++)
    {
    	$iTemp2 = ($x2 * $iNumPerPage2);
    	if ($iTemp2 < $iTtlNumItems2) // takes care of extra numbers after the overall final number
    	{
			
			
    		$iProxValor2 = $x2 + 1;
    		if ($iDBLoc2 == $iTemp2)
    		{
    			$strResultPagination2 = $strResultPagination2 . " " . $iProxValor2;
    		}
    		else
    		{
    			$strResultPagination2 = $strResultPagination2 .  " " . $sURLBeg2 . ($x2 * $iNumPerPage2) . $PrepParams2 . "')\">" . $iProxValor2 . "</a>";
    		}


		}
    	else
    		break;
    }
	
	
	$strResultPagination2 =  $strResultPagination2 . '</td><td>';

    // Print the "Next"
    if (($iDBLoc2 + $iNumPerPage2) < $iTtlNumItems2)
    {
    	$strResultPagination2 = $strResultPagination2 . "  " . $sURLBeg2 . ($iDBLoc2 + $iNumPerPage2) . $PrepParams2 . "')\"> >> </a>";
    }else{
		$strResultPagination2 = $strResultPagination2 . "";
    }

	$strResultPagination2 =  $strResultPagination2 . '</td><tr></table>';
    
    // Print the <<
    if ($iDBLocTemp2 >= $iNumPerPage2) 
    {
		/*
		$dVal2 = (( (int)($iDBLocTemp2 / $iNumPerPage2) ) * $iNumPerPage2 ^ 2) - ($iNumPerPage2 * 9);
		$strResultPagination2 = $strResultPagination2 . $sURLBeg2 . $dVal2 . "'><<</a> ";
		*/
    }
    
    
    
    // Print the >>
    if ($iTtlTemp2 > $iDBLocTemp2) 
    {
    	if (($iDBLocTemp2 + $iNumPerPage2) <= $iTtlTemp2)
    	{
    		// $strResultPagination2 = $strResultPagination2 .  " " . $sURLBeg2 . (( (int)($iDBLocTemp2 / $iNumPerPage2) ) * $iNumPerPage2 + $iNumPerPage2 ) * $iNumPerPage2 . "' class='paginador'>>></A> ";
    	}
    }
    
	
	if($PrepParams2 != "")
		$strResultPagination = str_replace($PrepParams2, '&' . $AdditionalParams2, $strResultPagination2);
	//***** End DISPLAY *****//
    
    if ( $iTtlNumItems2 == 0 )
		$PagAbsolutePosition2 = 0;

	
	//echo $strResultPagination2;exit();

	return $strResultPagination2;
}
?>