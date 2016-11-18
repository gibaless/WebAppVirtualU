<?
session_start();
include "includes/sitesettings_inc.php";
// Funcion que decripta un valor.
function decrypt2($string)
{
	if(!$string || $string == ""){return "";}
	
	$key = '123456789';
	$result = '';
	$string = base64_decode(urldecode($string));
	for($i=0; $i<strlen($string); $i++)
	{
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}
	
	return $result;
}

$strTipo = "";
$root = "";
$file = "";
$iId = "0";

if(isset($_REQUEST["t"]) && $_REQUEST["t"] != ""){
	$strTipo = $_REQUEST["t"];
}

if(isset($_REQUEST["id"]) && is_numeric(decrypt2($_REQUEST["id"]))){
	$iId = decrypt2($_REQUEST["id"]);
}

include "common/inc_database_open.php";
switch($strTipo){
	case "A":
			// Es un archivo.
			$Result = mysql_query("SELECT archivo_archivo FROM archivo WHERE archivo_archivo <> '' AND archivo_id = " . $iId);
			if($oRs = mysql_fetch_array($Result))
			{
				$file = $oRs["archivo_archivo"];
				$root = $DIR_ARCHIVOS;
			}
			mysql_free_result($Result);
			
		break;
	case "T":
			// Es un TP.
			$Result = mysql_query("SELECT tp_archivo FROM trabajo_practico WHERE tp_archivo <> '' AND tp_id = " . $iId);
			if($oRs = mysql_fetch_array($Result))
			{
				$file = $oRs["tp_archivo"];
				$root = $DIR_TPS;
			}
			mysql_free_result($Result);
		break;
	case "E":
			// Es una entrega.
			$Result = mysql_query("SELECT entrega_archivo FROM entrega WHERE entrega_archivo <> '' AND entrega_id = " . $iId);
			if($oRs = mysql_fetch_array($Result))
			{
				$file = $oRs["entrega_archivo"];
				$root = $DIR_ENTREGAS;
			}
			mysql_free_result($Result);
		break;
}
include "common/inc_database_close.php";

$file = basename($file);

$path = $root.$file;

$type = '';


if (is_file($path)) {

    $size = filesize($path);

    if (function_exists('mime_content_type')) {

        $type = mime_content_type($path);

    } else if (function_exists('finfo_file')) {

        $info = finfo_open(FILEINFO_MIME);

        $type = finfo_file($info, $path);

        finfo_close($info); 

    }

    if ($type == '') {

        $type = "application/force-download";

    }

    // Set Headers
	
    header("Content-Type: $type");
	
    header("Content-Disposition: attachment; filename=$file");

	header("Content-Transfer-Encoding: binary");

    header("Content-Length: " . $size);

    // Download File
	readfile($path);

} else {
	include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
	include "common/func_getparameter.inc";
	include "header.php"; ?>

    No existe el archivo<br />
	<div class="SubMenu">
	<a class="boton" href="javascript:history.back();">Volver</a>
	</div>
<?
	include "footer.php"; 
}

?>