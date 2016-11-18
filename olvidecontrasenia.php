<?
session_start();
header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
header("Expires: -100000"); //Expira pÃ¡gina

/* INCLUYO ARCHIVO DE CONFIGURACIÃ“N. */
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "common/func_getparameter.inc"; // AGREGO EL GETPARAMETER.
include "common/func_datetime.inc"; // AGREGO EL GETPARAMETER.

$MsgBoxMessage = "";
$strReturnUrl = "index.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];

	
		if (isset($_REQUEST["btnActualizar"]) ) 
		{
			include "common/inc_database_open.php";
		
			$strEmail = "";
			if(GetParameter("txtEmail") != ""){
				$strEmail = GetParameter("txtEmail");
			}
			
			// ARMO LA SENTENCIA DE UPDATE
			$strSQLComm = " SELECT usuario_nombre, usuario_email, usuario_password FROM usuario WHERE usuario_email = '" . SafeSql($strEmail) ."'";

			$Result = mysql_query($strSQLComm);

			if(mysql_num_rows($Result)){

				$oRMs = mysql_fetch_array($Result);
				//envio la pass via email
				$strNewPass = rand(100000, 999999);
				
				$strSQLComm = " UPDATE usuario SET " .
							  " usuario_password = '" . md5($strNewPass) . "' " .
							  " WHERE usuario_email = '" . SafeSql($strEmail) ."'";
				mysql_query($strSQLComm);
				
				$strUNombre = $oRMs['usuario_nombre'];
				$strUMail = $oRMs['usuario_email'];
				$strUPassword = $oRMs['usuario_password'];
			
				$mensaje = "Hola " . $strUNombre . ":";
				$mensaje .= "<br/><br/>Su contraseña de inicio de sesión ha sido modificada por: <b>" . $strNewPass  . "</b><br/><br/>Por favor, cuando ingrese al sitio cámbiela por una personal.";
			
				$asunto = 'Recuperación de contraseña de usuario.';
				include "enviarmail.php";
				?><script language="javascript" type="text/javascript">EnviarMail('<?=$strUNombre?>', '<?=$strUMail?>', '<?=$asunto?>', '<?=$mensaje?>' );</script><?
				$MsgBoxMessage = "La nueva contraseña ha sido enviada a su email.";
			}else{
				$MsgBoxMessage = "No existe un usuario con ese email.";
			}
			include "common/inc_database_close.php";
			
		}
	
include "header.php";
?>
<h2>Olvide Contraseña</h2>
<?
	if($MsgBoxMessage == ""){?>
	<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm" onsubmit="return validarForm(this);">
	<input type="hidden" name="return_url" value="<?=GetParameter("return_url")?>" />
	<!-- FORMULARIO DE EDICION -->
	<div class="Formulario">
		<div class="separaModulo">
			<div class="AcomodaLabel">
				E-mail:
			</div>
			<input type="text" name="txtEmail" id="txtEmail" value="" class="AcomodaTextBox" maxlength="50"/>
		</div>
		<div class="PosicionBotones" style="margin-left:160px;">
		<input class="boton" type="submit" name="btnActualizar" value="Enviar" />
		<input class="boton" type="button" value="Volver" onclick="self.location='<?=$strReturnUrl?>';" name="btnVolver" />
		</div>
	</div>
	</form>
	<!-- Inicio Validaciones -->
        <script language="JavaScript" type="text/javascript">
		var frmvalidator  = new Validator("frmForm");
		frmvalidator.EnableMsgsTogether();
		frmvalidator.addValidation("txtEmail","req","Debe ingresar su email.");
		</script>
	<!-- Fin Validaciones -->

	<? }else{ ?> <p style="margin-left: 40px;"><?=$MsgBoxMessage?></p> <? }
	
include "footer.php";
?>
