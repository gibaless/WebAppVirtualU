<?
/**********************************************************************
// Objetivo: Crea archivo de configuración del sistema.
'----------------------------------------------------------------------
// Autor: Lucas F. Ruano
// Fecha de creación: 1/10/2007
**********************************************************************/

function CrearArchivoPag($Nombre, $NombreAnterior, $Path)
{

	// SETEO VARIABLES.
	$APERTURA_SENTENCIA_PHP = chr("60") . "? ";
	$CIERRE_SENTENCIA_PHP   = "?" . chr("62");
	// FIN: SETEO VARIABLES.

	$strFileName = realpath("..") . $Path . "/" . $Nombre;

	// Si existe la pag. 1º hace una copia y despues borra el original.
	if($NombreAnterior != ""){
		//Ej: ../includes/
		$strPathAnteriorArchivo = realpath("..") . $Path . "/";
		
		/* Path del archivo el cual quiero crear. */
		// Ej: ../includes/site.asp
		$strNombreAnteriorArchivo = realpath("..") . $Path . "/" . $NombreAnterior;
		
		/* Path del archivo BACK */
		// Ej: ../includes/back_site.asp
		$strBackArchivo = realpath("..") . $Path . "/back_" . $NombreAnterior;
		
		if(file_exists($strNombreAnteriorArchivo))
		{
			// Verifico si existe el archivo BACK (si existe lo borro).
			if(file_exists($strBackArchivo)){
				unlink($strBackArchivo);
				//echo '<br><br>Borro el archivo: ' . $strBackArchivo;
			}
			
			// Renombro el archivo.
			if(!rename($strNombreAnteriorArchivo, $strPathAnteriorArchivo . "back_" . $NombreAnterior)){
				$MSG_ERROR = "El archivo " . $strNombreAnteriorArchivo . " no puede ser renombrado.";
				//echo '<br><br>no pudo ser renombrado: ' . $strNombreAnteriorArchivo;
			}
				//echo '<br><br>Se renombro el archivo sitesetting por : ' . $strPathAnteriorArchivo . "back_" . $NombreAnterior;

		}
	}

	// Si no existe el archivo lo creo.
	if(!file_exists($strFileName))
	{
		
		//echo '<br><br>Si no existe el sitesettings lo creo.';
		
		$strVariable = "";
		$strValor	 = "";
		
		include "../common/inc_database_open.php";
		
		// OBTENGO LISTADO DE CONFIGURACIONES.
		$Result = mysql_query("SELECT * FROM site_settings ORDER BY variable");
	 	if(mysql_num_rows($Result)){
			// ABRO EL ARCHIVO (Si no existe lo crea, de lo contrario escribe en el final).
			if($fp = fopen($strFileName, "a")){
				$write = fputs($fp, $APERTURA_SENTENCIA_PHP);
				
				$HuboDatos = false;
				while ( $oRs = mysql_fetch_array($Result) )
				{
					$HuboDatos 	 = true;
					$strVariable = trim("" . $oRs["variable"]);
					$strValor 	 = trim("" . $oRs["valor"]);
					
					// Armo lista de string para escribir luego en el archivo.
					//$write = fputs($fp, "");
					$write = fputs($fp, $strVariable . " = " . $strValor . ";");
				}
				
				//$write = fputs($fp, "");
				$write = fputs($fp, $CIERRE_SENTENCIA_PHP); // ESCRIBO EN EL ARCHIVO.

			}
			
			// CIERRO EL ARCHIVO.
			fclose($fp);
			
		}

		/* Liberar conjunto de resultados */
		mysql_free_result($Result);
		
		include "../common/inc_database_close.php";
	}
}
?>