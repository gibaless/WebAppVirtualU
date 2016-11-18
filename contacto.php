<?
session_start();

/* INCLUYO ARCHIVO DE CONFIGURACIÃ“N. */
include "includes/sitesettings_inc.php";
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
include "common/func_pagination_www.inc";

header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
header("Expires: -100000"); //Expira pÃ¡gina

$MsgBoxMessageOK = "";

if ( GetParameter("btnActualizar") != "" ) 
{
	$sustituye = array("\r\n", "\n\r", "\n", "\r", "\"");
    
	$strComentario = str_replace(chr(13), "<br>", str_replace("'", "", GetParameter("mensaje")));
	$strComentario = str_replace($sustituye, "", $strComentario);
	
	$strNombre = str_replace(chr(13), "<br>", str_replace("'", "", GetParameter("nombre")));
	$strNombre = str_replace($sustituye, "", $strNombre);
	
	$strMail = str_replace(chr(13), "<br>", str_replace("'", "", GetParameter("mail")));
	$strMail = str_replace($sustituye, "", $strMail);
	
	$strTitulo = str_replace(chr(13), "<br>", str_replace("'", "", GetParameter("titulo")));
	$strTitulo = str_replace($sustituye, "", $strTitulo);
	
	$strMensaje = "Este mensaje fue enviado por: " . $strNombre . " <br>";
	$strMensaje .= " Su e-mail es: " . $strMail . " <br><br>";
	$strMensaje .= " Mensaje: " . $strComentario . " <br>";
	
	$strNombre = "VirtualU";
	$strMail = $EMAIL_INFO;
	$strTitulo = '** Contacto desde VirtualU.com.ar: ' . $strTitulo;
	
	include "enviarmail.php";
	?><script language="javascript" type="text/javascript">EnviarMail('<?=$strNombre?>', '<?=$strMail?>', '<?=$strTitulo?>', '<?=$strMensaje?>' );CerrarPopup();</script><?
	$MsgBoxMessageOK = "Mensaje enviado exitosamente.";

	
}

include "header.php";
?>
<h3>Cont&aacute;ctenos</h3>
<?
if ($MsgBoxMessageOK != "") 
{
?>
	<div align="center">
		<p style="font-size:12px; font-weight:bold;"><?=$MsgBoxMessageOK?></p>
		<br /><br />
		<input class="boton" type="button" value="Volver" onclick="self.location='contacto.php';" name="btnVolver2">
	</div>
<?
}else{?>
	<p style="margin-left: 40px;">Utilice el siguiente formulario para contactarse con nosotros. </p>
	<div style="margin-left: 100px; margin-top: 10px; margin-right: 360px; border: 1px solid  ">
	
	
	<form method="post" action="contacto.php" style="text-align:left; background: #e0eee0; padding: 20px;" name="Formulario3">	
	<label for="nombre"><b> Nombre: (*) </b></label>
	<div align="left"><input id="nombre" class="login" name="nombre" class="element text medium" type="text" maxlength="60" value="" style="width: 240px;"/></div> 
	<br/><label for="mail"><b> Correo Electr&oacute;nico: (*)</b></label>
	<div align="left"><input id="mail" class="login"  name="mail" class="element text medium" type="text" maxlength="50" value="" style="width: 240px;"/> </div> 
	<br/><label for="titulo"><b> Titulo: </b></label>
	<div align="left"><input id="titulo" class="login"  name="titulo" class="element text medium" type="text" maxlength="255" value="" style="width: 440px;"/> </div> 
	<br/><label for="mensaje"><b> Dejanos Tu Consulta o Comentario: (*) </b></label>
	<div align="left"><textarea class="login" id="mensaje" name="mensaje" style="width: 440px; height: 140px;" onkeydown="if(this.value.length > 500){ alert('Has superado el tamaño máximo permitido'); this.value = this.value.substring(0, 500); return false; }"></textarea><br />
					<small><b>* Máximo 500 caracteres</b></small>
	</div> 
	<input id="btnActualizar" style="margin-left: 160px; margin-top: 20px;" type="submit" name="btnActualizar" value="ENVIAR" class="boton" />
	<input type="reset" name="reset" value="LIMPIAR" class="boton" />
	</form>
			<!-- Inicio Validaciones -->
			<script language="JavaScript" type="text/javascript">
			var frmvalidator  = new Validator("Formulario3");
			frmvalidator.EnableMsgsTogether();
			frmvalidator.addValidation("nombre","req","Debe ingresar su nombre.");
			frmvalidator.addValidation("mail","req","Debe ingresar su mail de contacto.");
			frmvalidator.addValidation("mail","email","Debe ingresar un mail válido de contacto.");
			frmvalidator.addValidation("mensaje","req","Debe ingresar su consulta o comentario.");
			</script>
			<!-- Fin Validaciones -->
	
	</div>
<? 
}
include "footer.php"; ?>