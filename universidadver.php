<?
session_start();

/* INCLUYO ARCHIVO DE CONFIGURACIÓN. */
include "includes/sitesettings_inc.php";
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.

header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
//header("ExpiresAbsolute: " . time() -10); //Expira página Absolutamente
header("Expires: -100000"); //Expira página

$strMsqlError = "";
$MsgBoxMessage = "";
$BoxWidth = "";
$BoxEdit = "Off";
$BoxHelp = "Off";
$current0 = "On";

$strFile = "";

include "common/func_getparameter.inc";
include "common/func_datetime.inc";
$bHayError = FALSE;

$strReturnUrl = "universidades.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];

$iId = "0";
if(GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id")))){
	$iId = decrypt(GetParameter("id"));
}

include "common/inc_database_open.php";

// Obtengo la universidad.
$strSQLCommUniversidades = " SELECT U.*, PE.estado, P.pais_titulo " .
				" FROM universidad AS U " . 
				" INNER JOIN provincias_estados AS PE ON U.prov_id = PE.prov_id " . 
				" INNER JOIN pais AS P ON PE.pais_id = P.pais_id " . 
				" WHERE U.activo = 'Y' " .
				" AND P.activo = 'Y' " .
				" AND U.universidad_id =  " . SafeSql($iId);
				
$Result = mysql_query($strSQLCommUniversidades);


include "header.php";

?>
<script language="JavaScript" type="text/javascript">

var XMLHttpRequestObject = false;  
if (window.XMLHttpRequest) {  
	XMLHttpRequestObject = new XMLHttpRequest();  
} else if (window.ActiveXObject) {  
	XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");  
}  


function TraeProvincias(){  
	if(XMLHttpRequestObject){
		var thisForm = window.document.forms['frmForm'];
		var iIdPais = thisForm.cmbPais.value; 
		var iIdProvincia = '<?=GetParameter("cmbProvincia")?>';   
		
		XMLHttpRequestObject.open("GET", "ajax_provincias.php?idp=" + iIdPais + "&id=" + iIdProvincia);  
		XMLHttpRequestObject.onreadystatechange = ajaxRespuesta;
		XMLHttpRequestObject.send(null);  
	}   
}

function ajaxRespuesta(){  
	if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {  
		select_innerHTML(document.getElementById("cmbProvincia"), XMLHttpRequestObject.responseText);  
	}  
}
	
function validarForm(thisForm) {
	
	if(thisForm.txtUniversidad.value == ''){
		alert('Debe ingresar el nombre de la universidad.');
		thisForm.txtUniversidad.focus();
		return false;
	}
	if(thisForm.cmbProvincia.value == ''){
		alert('Debe seleccionar una provincia.');
		thisForm.cmbProvincia.focus();
		return false;
	}
	if(thisForm.txtTelefono.value == '' && thisForm.txtWeb.value == '' && thisForm.txtEmail.value == ''){
		alert('Debe ingresar al menos un dato de contacto.');
		thisForm.txtTelefono.focus();
		return false;
	}
	
	return true;
}
</script>
<h2>Detalle de Universidad</h2>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm" onsubmit="return validarForm(this);">
	<input type="hidden" name="action" value="<?=GetParameter("action")?>">
	<input type="hidden" id="HidFecha" name="HidFecha">
	<input type="hidden" name="return_url" value="<?=GetParameter("return_url")?>" />
	<!-- FORMULARIO DE EDICION -->
	<div class="Formulario">
		<? if(mysql_num_rows($Result)){
			if($oRs = mysql_fetch_array($Result)){?>
				<div class="separaModulo">
					<div class="AcomodaLabel">
						Universidad: 
					</div>
					&nbsp;
					<?=$oRs["universidad_titulo"]?>
				</div>
				<div class="separaModulo">
					<div class="AcomodaLabel">
						Pa&iacute;s:
					</div>
					&nbsp;
					<?=$oRs["pais_titulo"]?>
				</div>
				<div class="separaModulo">
					<div class="AcomodaLabel">
						Provincia:
					</div>
					&nbsp;
					<?=$oRs["estado"]?>
				</div>
				<div class="separaModulo">
					<div class="AcomodaLabel">
						Ciudad:
					</div>
					&nbsp;
					<?=$oRs["universidad_ciudad"]?>
				</div>
				<div class="separaModulo">
					<div class="AcomodaLabel">
						Direcci&oacute;n:
					</div>
					&nbsp;
					<?=$oRs["universidad_direccion"]?>
				</div>
				<div class="separaModulo">
					<div class="AcomodaLabel">
						Tel&eacute;fono:
					</div>
					&nbsp;
					<?=$oRs["universidad_telefono"]?>
				</div>
				<div class="separaModulo">
					<div class="AcomodaLabel">
						E-mail:
					</div>
					&nbsp;
					<? if($oRs["universidad_email"] != ""){?><a href="mailto:<?=$oRs["universidad_email"]?>" target="_blank"><?=$oRs["universidad_email"]?></a><? }?>
				</div>
				<div class="separaModulo">
					<div class="AcomodaLabel">
						Web:
					</div>
					&nbsp;
					<? if($oRs["universidad_web"] != ""){?><a href="http://<?=$oRs["universidad_web"]?>" target="_blank"><?=$oRs["universidad_web"]?></a><? }?>
				</div>
			</div>
	<?	}
	}else{?>
		<p style="margin-left:30px; font-size:11px;"><img src="images/errormsg.png" title="Error" style=" vertical-align:middle;"/>&nbsp;&nbsp;&nbsp;		
		Datos erroneos.</p>
	<?
	}
	mysql_free_result($Result);?>
	<div class="PosicionBotones">
		<input class="boton" type="button" value="Volver" onclick="self.location='<?=$strReturnUrl?>';" name="btnVolver">
	</div>
	<!-- FIN DE: FORMULARIO DE EDICION -->
</form>
<?
include "common/inc_database_close.php";
include "footer.php";
?>