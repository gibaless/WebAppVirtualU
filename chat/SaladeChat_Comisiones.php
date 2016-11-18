<?
session_start();
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
/* INCLUYO ARCHIVO DE CONFIGURACIÓN. */
include "../includes/sitesettings_inc.php";
include "../includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "../common/func_getparameter.inc";
include "../common/func_datetime.inc";
include "../common/func_pagination_www.inc";
require_once dirname(__FILE__)."/src/pfcinfo.class.php";
require_once dirname(__FILE__)."/src/phpfreechat.class.php";

$iIdComision= "";
if(GetParameter("idc") != "" && is_numeric(decrypt(GetParameter("idc"))) ){
	$iIdComision = decrypt(GetParameter("idc"));
}
include "../common/inc_database_open.php";
$strParams = '';
//Consulta SQL para traer las materias en la que estoy inscripto
$strSQLComm = " SELECT C.comision_codigo AS codigo, C.comision_titulo AS comision, M.materia_titulo AS materia, R.carrera_titulo AS carrera " .
				" FROM usuario_comision AS UC " . 
				" INNER JOIN usuario AS U ON U.usuario_id = UC.usuario_id " . 
				" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
				" INNER JOIN materia AS M ON (M.materia_id = C.materia_id AND M.activo = 'Y') " . 
				" INNER JOIN carrera AS R ON (R.carrera_id = M.carrera_id AND R.activo = 'Y') " . 
				" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
				" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
				" WHERE U.activo = 'Y' " .
				" AND UC.activo = 'Y' " .
				" AND C.comision_fechahasta >= CURDATE()" .
				" AND C.comision_id = " . $iIdComision . 
				" AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]);

	$Result2 = mysql_query($strSQLComm);

	$oRs = mysql_fetch_array($Result2);
	$strComision = $oRs['comision'];
	$strComisionCodigo = $oRs['codigo'];
	$strMateria = $oRs['materia'];
	$strCarrera = $oRs['carrera'];

$TituloSala = "Sala ". $strComision . "(" . $strComisionCodigo. ") <br/>" . $strCarrera ." >> " . $strMateria;
$iIdSala = "Sala ". $strComision . $strComisionCodigo . $iIdComision;
$NombreSala = "Sala ". $strComision . "(".$strComisionCodigo.")";
$params = array();
$params["serverid"] = md5($iIdSala); // calculate a unique id for this chat
$params["title"] = $NombreSala;
$params["max_msg"] = 0;
$params['firstisadmin'] = true;
	$nombre = $_SESSION["FirstName"];
		$id = $_SESSION["UserId"];
		$apellido = $_SESSION["LastName"];
		$tipo_usuario = $_SESSION["UserAccess"];
		//$RequireAccess = "A|L|P|Y";
		switch ($tipo_usuario) {
		case 'A':
			$tipo_usuario= "ad_";  break;
		case 'L':
			$tipo_usuario= "al_";  break;
		case 'P':
			$tipo_usuario= "pr_";  break;
		case 'Y':
			$tipo_usuario= "ay_";  break;
}
$params["max_nick_len"] = 25;
$params["nick"] = $tipo_usuario.$nombre."_".$apellido."_".$id.rand(1,99);  // setup the intitial nickname
$params["isadmin"] = false; // makes everybody admin: do not use it on production servers ;)
$params["debug"] = false;
$params["language"] = "es_ES";
$params["height"] = "400px";
$params["channels"] = array($NombreSala);
//$params["theme"] = "blune";
//$params["theme"] = "cerutti"; 
//$params["theme"] = "msn"; 
$params["theme"] = "phpbb2";
$params["nickname_colorlist"] = array('#FF0000','#339933','#0000FF');
$chat = new phpFreeChat( $params );
$info  = new pfcInfo( md5($iIdSala) );
$users = $info->getOnlineNick(NULL);

$RequireAccess = "A|L|P|Y";
include "../access.php";
include "../header2.php"; 
/* Variables del chat */
$info = "";
$nb_users = count($users);

?>
<h3><?=$TituloSala?></h3>

<div style="margin-left: 10px; width: 960px; height: 400px; ">
<?

$chat->printChat();
if ($nb_users <= 1){
  $info = "<strong>%d</strong> usuario conectado.";
  $nb_users = 1;
}
else
  $info = "<strong>%d</strong>  usuarios conectados.";
echo "<p align='right'>".sprintf($info, $nb_users)."</p>";

?>
</div>
<?
include "../common/inc_database_close.php";
 include "../footer2.php"; ?>
