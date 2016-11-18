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

// Defino variable de acceso para el INCLUDE: Access
$RequireAccess = "A|P|Y";

include "access.php";
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
$bHayError = FALSE;

$strReturnUrl = "universidades.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];
	
	if ( GetParameter("btnActualizar") != "" ) 
	{
		$strWeb = str_replace("https://", "", GetParameter("txtWeb"));	
		$strWeb = str_replace("HTTPS://", "", $strWeb);	
		$strWeb = str_replace("http://", "", $strWeb);	
		$strWeb = str_replace("HTTP://", "", $strWeb);	
		
		if ( GetParameter("action") == "new" && $MsgBoxMessage == "" )
		{
			include "common/inc_database_open.php";
			
			
			
			// Armo la sentencia de INSERT
			$strSQLComm = " INSERT INTO universidad(universidad_titulo, universidad_direccion, universidad_ciudad, universidad_telefono, universidad_email, universidad_web, prov_id, activo) " .
							" VALUES (" .
							"'" . SafeSql(GetParameter("txtUniversidad")) . "'," . 
							"'" . SafeSql(GetParameter("txtDireccion")) . "'," . 
							"'" . SafeSql(GetParameter("txtCiudad")) . "'," . 
							"'" . SafeSql(GetParameter("txtTelefono")) . "'," . 
							"'" . SafeSql(GetParameter("txtEmail")) . "'," . 
							"'" . SafeSql($strWeb) . "'," . 
							SafeSql(GetParameter("cmbProvincia")) . "," .
							" 'Y'" .
							")";
			mysql_query($strSQLComm);

			// Obtengo si ocurrió algún error
			if(mysql_error() != ""){
				//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
				$MsgBoxMessage = "No se pudo crear la universidad, por favor inténtelo nuevamente.";
			}
			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $MsgBoxMessage == "" )
			{
				header("Location:" . $strReturnUrl);
				exit();
			}
			
		}
	}
	// FIN DE: Si se agrega un nuevo registro
include "header.php";
include "common/inc_database_open.php";

//Obtengo Listado de Paises
$strSQLCommPaises = " SELECT pais_id, pais_titulo " .
			  " FROM pais " .
			  " WHERE activo = 'Y' ORDER BY pais_titulo  ";
$Result2 = mysql_query($strSQLCommPaises);

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
<h2>Alta de Universidad</h2>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm" onsubmit="return validarForm(this);">
	<input type="hidden" name="action" value="<?=GetParameter("action")?>">
	<input type="hidden" id="HidFecha" name="HidFecha">
	<input type="hidden" name="return_url" value="<?=GetParameter("return_url")?>" />
	<!-- FORMULARIO DE EDICION -->
	<?
	// Si hay un error muestro mensaje y termino
	if ($MsgBoxMessage != "") 
	{?>
		<p style="margin-left:30px; font-size:11px;"><img src="images/errormsg.png" title="Error" style=" vertical-align:middle;"/>&nbsp;&nbsp;&nbsp;		
		<?=$MsgBoxMessage?></p>
	<? }?>
	<div class="Formulario">
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Universidad: (*)
			</div>
			<input type="text" id="txtUniversidad" name="txtUniversidad" value="<?=GetParameter("txtUniversidad")?>" maxlength="255" class="AcomodaTextBox" />
		</div>
		<div class="separaModulo">
        	<div class="AcomodaLabel">
				Pa&iacute;s: (*)
		</div>
            		
			<select class="AcomodaDdl" name="cmbPais" id="cmbPais" onchange="TraeProvincias();">
				<option value="">--Seleccionar--</option>
				<?
				if(mysql_num_rows($Result2)){
					while($oRs = mysql_fetch_array($Result2))
					{?>
						<option value="<?=$oRs["pais_id"]?>"<? if (trim("".$oRs["pais_id"]) == trim("".GetParameter("cmbPais"))){?> selected="selected"<? }?>><?=$oRs["pais_titulo"]?></option>
					<?
					}
				}
				mysql_free_result($Result2);
				?>
			</select>
        </div>
		<div class="separaModulo">
        	<div class="AcomodaLabel">Provincia: (*)</div>

			<div id="dvProvincia">
			<select name="cmbProvincia" id="cmbProvincia" class="AcomodaDdl" >
					<option value="">--Seleccionar--</option>
			</select>
			</div>
        </div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Ciudad:
			</div>
			<input type="text" id="txtCiudad" name="txtCiudad" value="<?=GetParameter("txtCiudad")?>" maxlength="255" class="AcomodaTextBox" />
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Direcci&oacute;n:
			</div>
			<input type="text" id="txtDireccion" name="txtDireccion" value="<?=GetParameter("txtDireccion")?>" maxlength="255" class="AcomodaTextBox" />
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Tel&eacute;fono:
			</div>
			<input type="text" id="txtTelefono" name="txtTelefono" value="<?=GetParameter("txtTelefono")?>" maxlength="255" class="AcomodaTextBox" />
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				E-mail:
			</div>
			<input type="text" id="txtEmail" name="txtEmail" value="<?=GetParameter("txtEmail")?>" maxlength="255" class="AcomodaTextBox" />
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Web:
			</div>
			<small>http://</small><input type="text" id="txtWeb" name="txtWeb" value="<?=GetParameter("txtWeb")?>" maxlength="255" class="AcomodaTextBox" />
		</div>
	</div>
	<div class="PosicionBotones">
		<input class="boton" type="submit" name="btnActualizar" value="Crear">
		<input class="boton" type="button" value="Volver" onclick="self.location='<?=$strReturnUrl?>';" name="btnVolver">
	</div>
	<!-- FIN DE: FORMULARIO DE EDICION -->
</form>
<?
include "common/inc_database_close.php";
include "footer.php";
?>
