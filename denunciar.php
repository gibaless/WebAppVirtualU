<?
session_start();
header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
//header("ExpiresAbsolute: " . time() -10); //Expira página Absolutamente
header("Expires: -100000"); //Expira página

/* INCLUYO ARCHIVO DE CONFIGURACIÓN. */
include "includes/sitesettings_inc.php";
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "common/func_getparameter.inc";
include "common/func_datetime.inc";

$strMsqlError = "";
$MsgBoxMessage = "";
$MsgBoxMessageOK = "";
$BoxWidth = "";
$BoxEdit = "Off";
$BoxHelp = "Off";
$current0 = "On";
$strFile = "";

// Defino variable de acceso para el INCLUDE: Access
$RequireAccess = "A|L|P|Y";
include "access.php";
$bHayError = FALSE;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>VirtualU - Práctica en Linea</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="Robots" content="NOINDEX" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />
<link rel="stylesheet" type="text/css" href="css/layout.css" />
<script type="text/javascript" src="js/stmenu.js"></script>
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/validator.js"></script>
<script type="text/javascript" src="js/functions.js"></script>
</head>
<body>
<div id="contPopUp">
<h2>Denunciar</h2>
<?

$strTipo = "";
if(GetParameter("t") != ""){
	$strTipo = GetParameter("t");
}

$iId = "0";
if(GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id")))){
	$iId = decrypt(GetParameter("id"));
}


if ( GetParameter("btnActualizar") != "" ) 
{
	include "common/inc_database_open.php";
	
	$strComentario = str_replace(chr(13), "<br>", str_replace("'", "", GetParameter("txtComentario")));
	$sustituye = array("\r\n", "\n\r", "\n", "\r", "\"");
    $strComentario = str_replace($sustituye, "", $strComentario);
	$strSQLComm = "";
	switch($strTipo){
		case "A":
			// Denuncio el archivo.
			$strSQLComm = " UPDATE archivo SET " .
						  " activo = 'P' " .
						  " WHERE activo = 'Y' AND archivo_id = " . SafeSql($iId);
			$strTipo = "Archivo";
			break;
		case "M":
			// Denuncio el mensaje.
			$strSQLComm = " UPDATE mensaje SET " .
						  " mensaje_activo = 'P' " .
						  " WHERE mensaje_activo = 'Y' AND mensaje_id = " . SafeSql($iId);
			$strTipo = "Mensaje";
			break;
		default:
			$MsgBoxMessage = "Datos erroneos.";
			break;
	}
	
	if($MsgBoxMessage == ""){
		mysql_query($strSQLComm);
	
		// Obtengo si ocurrió algún error
		if(mysql_error() != ""){
			//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
			$MsgBoxMessage = "Datos erroneos.";
		}
	}
	include "common/inc_database_close.php";
	
	// Si No hay error, direcciono al BROWSE
	if ( $MsgBoxMessage == "" )
	{
		$strNombre = "VirtualU";
		$strMail = $EMAIL_INFO;
		$strTitulo = "Denuncia de " . $strTipo . " id = " . $iId;
		$strMensaje = "El usuario " . $_SESSION["UserName"] . " (" . $_SESSION["UserId"] . ")" . " realizó la siguiente denuncia:<br><br>" . $strComentario;
		include "enviarmail.php";
		?><script language="javascript" type="text/javascript">EnviarMail('<?=$strNombre?>', '<?=$strMail?>', '<?=$strTitulo?>', '<?=$strMensaje?>' );CerrarPopup();</script><?
		$MsgBoxMessageOK = "Se ha realizado la denuncia y será evaluada por los administradores del sitio.";
	}
	
}
// FIN DE: Si se agrega un nuevo registro

include "common/inc_database_open.php";

switch($strTipo){
	case "A":
		// Obtengo el archivo.
		$strSQLCommArchivos = " SELECT A.archivo_id " .
							  " FROM archivo AS A " .
							  " WHERE A.archivo_id = " . SafeSql($iId);
		$Result = mysql_query($strSQLCommArchivos);
			
		if ( $oRs = mysql_fetch_array($Result) )
		{
			$iId = $oRs["archivo_id"];
		}else{
			$MsgBoxMessage = "Datos erroneos.";
		}
		mysql_free_result($Result);
		break;
	case "M":
		// Obtengo el mensaje.
		$strSQLCommMensaje = " SELECT M.mensaje_id " .
							  " FROM mensaje AS M " .
							  " WHERE M.mensaje_id = " . SafeSql($iId);
		$Result = mysql_query($strSQLCommMensaje);
			
		if ( $oRs = mysql_fetch_array($Result) )
		{
			$iId = $oRs["mensaje_id"];
		}else{
			$MsgBoxMessage = "Datos erroneos.";
		}
		mysql_free_result($Result);
		break;
	default:
		$MsgBoxMessage = "Datos erroneos.";
		break;
}

if ($MsgBoxMessageOK != "") 
{
?>
	<div align="center">
		<p style="font-size:12px; font-weight:bold;"><?=$MsgBoxMessageOK?></p>
		<br /><br />
		<input class="boton" type="button" value="Volver" onclick="CerrarPopup();" name="btnVolver2">
	</div>
<?
}else{?>
	<script language="JavaScript" type="text/javascript">
		function validarForm(thisForm) {
			if(thisForm.txtComentario.value == ''){
				alert('Debe ingresar el comentario de la denuncia.');
				thisForm.txtComentario.focus();
				return false;
			}
			
			
			return true;
		}
		
		

	</script>
	<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm" onsubmit="return validarForm(this);">
		<input type="hidden" id="HidFecha" name="HidFecha">
		<input type="hidden" name="t" value="<?=GetParameter("t")?>"/>
		<input type="hidden" name="id" value="<?=GetParameter("id")?>"/>
		<input type="hidden" name="return_url" value="<?=GetParameter("return_url")?>" />
		<!-- FORMULARIO DE EDICION -->
		<?
		// Si hay un error muestro mensaje y termino
		if ($MsgBoxMessage != "") 
		{?>
			<p style="margin-left:30px; font-size:11px;"><img src="images/errormsg.png" title="Error" style=" vertical-align:middle;"/>&nbsp;&nbsp;&nbsp;		
			<?=$MsgBoxMessage?></p>
		<? }?>
		<div class="FormularioPopUp" >
			<div class="separaModulo">
				<div class="AcomodaLabel">
					Comentario: (*)
				</div>
				<textarea type="text" id="txtComentario" name="txtComentario" rows="8" style="width:254px;" onkeydown="if(this.value.length > 500){ alert('Has superado el tamaño máximo permitido'); this.value = this.value.substring(0, 500); return false; }"><?=GetParameter("txtComentario")?></textarea>
				<br />
				<small><b>* Máximo 500 caracteres</b></small>
		</div>
		<div class="PosicionBotonesPopUp">
			<? if($MsgBoxMessage == ""){?><input class="boton" type="submit" name="btnActualizar" value="Enviar"><? }?>
			<input class="boton" type="button" value="Volver" onclick="CerrarPopup();" name="btnVolver">
		</div>
		<!-- FIN DE: FORMULARIO DE EDICION -->
	</form>
<?
}
include "common/inc_database_close.php";
?>
</div>
</body>
</html>