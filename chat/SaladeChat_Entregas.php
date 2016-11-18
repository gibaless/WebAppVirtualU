<?
session_start();
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
require_once dirname(__FILE__)."/src/pfcinfo.class.php";
require_once dirname(__FILE__)."/src/phpfreechat.class.php";
$params = array();
$params["serverid"] = md5("Sala Entregas"); // calculate a unique id for this chat
$params["title"] = "Sala Entregas";

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
$params["max_msg"] = 0;
$params["nick"] = $tipo_usuario.$nombre."_".$apellido."_".$id.rand(1,99);  // setup the intitial nickname
$params["isadmin"] = false; // makes everybody admin: do not use it on production servers ;)
$params["debug"] = false;
$params["language"] = "es_ES";
$params["height"] = "400px";
$params["channels"] = array("Sala de Entregas");
//$params["theme"] = "blune";
//$params["theme"] = "cerutti"; 
//$params["theme"] = "msn"; 
$params["theme"] = "phpbb2";
$params["nickname_colorlist"] = array('#FF0000','#339933','#0000FF');
$chat = new phpFreeChat( $params );

$info  = new pfcInfo( md5("Sala Entregas") );
$users = $info->getOnlineNick(NULL);



/* INCLUYO ARCHIVO DE CONFIGURACIÓN. */
include "../includes/sitesettings_inc.php";
include "../includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "../common/func_getparameter.inc";
include "../common/func_datetime.inc";
include "../common/func_pagination_www.inc";

$RequireAccess = "A|L|P|Y";
include "../access.php";

include "../header2.php"; 
/* Variables del chat */
$info = "";
$nb_users = count($users);

?>
<h3>Sala de Entregas</h3>

<div style="margin-left: 10px; width: 960px; height: 400px; ">

<? $chat->printChat(); 

if ($nb_users <= 1){
  $info = "<strong>%d</strong> usuario conectado.";
	$nb_users = 1 ;
}else
  $info = "<strong>%d</strong>  usuarios conectados.";

 echo "<p align='right'>".sprintf($info, $nb_users)."</p>";
?>
</div>
<? include "../footer2.php"; ?>
