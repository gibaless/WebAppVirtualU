<?
/**********************************************************************
' ** FUNCTION **
' Nombre: getparameter.inc
' Objetivo: Funcion que me devuelve un parametro del REQUEST.
'----------------------------------------------------------------------
' Autor: Gerardo J. Ruano
' Fecha de creación: 06/09/2002
 **********************************************************************/

// Parámetros
//	- ParameterName: Nombre del parámetro del se quiere el valor.

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