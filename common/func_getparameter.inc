<?
/**********************************************************************
' ** FUNCTION **
' Nombre: getparameter.inc
' Objetivo: Funcion que me devuelve un parametro del REQUEST.
'----------------------------------------------------------------------
' Autor: Gerardo J. Ruano
' Fecha de creaci�n: 06/09/2002
 **********************************************************************/

// Par�metros
//	- ParameterName: Nombre del par�metro del se quiere el valor.

// Nota: Si no lo encuentra, retorna vacio.

function GetParameter($ParameterName)
{
	if ( isset($_POST[$ParameterName]) && $_POST[$ParameterName] != null )
		return($_POST[$ParameterName]);
	else if ( isset($_GET[$ParameterName]) && $_GET[$ParameterName] != null )
		return($_GET[$ParameterName]);
	else
		return ("");
}
?>