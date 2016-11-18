<?
session_start();

/* INCLUYO ARCHIVO DE CONFIGURACIÃ“N. */
include "includes/sitesettings_inc.php";
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "common/func_getparameter.inc"; // AGREGO EL GETPARAMETER.
include "common/func_datetime.inc"; // AGREGO EL GETPARAMETER.

header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
header("Expires: -100000"); //Expira pÃ¡gina

$MsgBoxMessage = "";
$BoxTitle = "";
$strReturnUrl = "micuenta.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];

if(GetParameter("e") != ""){
	include "common/inc_database_open.php";
	// Termino el proceso de registración
	$strSQLCommVerifico = " SELECT * " .
					  " FROM usuario " .
					  " WHERE usuario_id = '" . decrypt(GetParameter("e")) . "'" . 
					  " AND activo = 'P'";
	$Result = mysql_query($strSQLCommVerifico);
	if(mysql_num_rows($Result)){
		if ( $oRs = mysql_fetch_array($Result) )
		{
			
			// ARMO LA SENTENCIA DE UPDATE
			$strSQLComm = " UPDATE usuario SET " .
						" activo = 'Y'" .
						" WHERE usuario_id = " . SafeSql($oRs["usuario_id"]);
			mysql_query($strSQLComm);
						
			$_SESSION["UserLogged"] = 1;
			$_SESSION["UserId"] = $oRs["usuario_id"];
			$_SESSION["UserName"] = $oRs["usuario_email"];
			$_SESSION["FirstName"] = $oRs["usuario_nombre"];
			$_SESSION["LastName"] = $oRs["usuario_apellido"];
			$_SESSION["UserAccess"] = $oRs["tipo_usuario"];
			$MsgBoxMessage = "Registraci&oacute;n exitosa!!!<br>Ya puede disfrutar de los beneficios de VirtualU!!!.";
		}
	}
	mysql_free_result($Result);
	include "common/inc_database_close.php";
}

$strActionLogin = "new";
if (  isset($_SESSION["UserLogged"]) && $_SESSION["UserLogged"] == 1 )	$strActionLogin = "edit";
	
	
	if ( isset($_POST["btnActualizar"]) ) 
	{
		
		include "common/inc_database_open.php";
		
		
		$strNombre = "";
		if(GetParameter("txtNombre") != ""){
			$strNombre = GetParameter("txtNombre");		
		}
		
		$strApellido = "";
		if(GetParameter("txtApellido") != "" ){
			$strApellido = GetParameter("txtApellido");		
		}
		
		$iIdPais = "";
		if(GetParameter("cmbPaisId") != "" && is_numeric(GetParameter("cmbPaisId"))){
			$iIdPais = GetParameter("cmbPaisId");
		}
		
		$iIdProvincia = "0";
		if(GetParameter("cmbProvincia") != "" && is_numeric(GetParameter("cmbProvincia"))){
			$iIdProvincia = GetParameter("cmbProvincia");
		}
		
		$strTipoUsuario = ""; //Acceso del usuario
		if(GetParameter("cmbTipoUsuario") != ""){
			$strTipoUsuario = GetParameter("cmbTipoUsuario");
		}
		
		$strEmail = ""; 
		if(GetParameter("txtEmail") != ""){
			$strEmail = GetParameter("txtEmail");
		}
		
		$strPassword = "";
		if(GetParameter("txtPassword") != ""){
			$strPassword = md5(GetParameter("txtPassword"));
		}
		
		$iIdTipoDni = ""; 
		if(GetParameter("cmbTipoDni") != ""){
			$iIdTipoDni = GetParameter("cmbTipoDni");
		}
		
		$strDni = ""; 
		if(GetParameter("txtDni") != "" ){
			$strDni = GetParameter("txtDni");
		}
		
		$strCiudad = ""; 
		if(GetParameter("txtCiudad") != "" ){
			$strCiudad = GetParameter("txtCiudad");
		}
		$strFechaNac="01/01/2000";
		if(  GetParameter("fecha_1_1") != '' && GetParameter("fecha_1_2")  != '' && GetParameter("fecha_1_3")  != '' ){
			$strFechaNac = $_POST['fecha_1_1'] ."/". $_POST['fecha_1_2'] . "/". $_POST['fecha_1_3'];
		}
		
		$strTelefono = ""; 
		if(GetParameter("txtTelefono") != "" ){
			$strTelefono = GetParameter("txtTelefono");
		}
		$strCelular = ""; 
		if(GetParameter("txtCelular") != "" ){
			$strCelular = GetParameter("txtCelular");
		}
		
		$strNotificaciones = ""; 
		if(GetParameter("cmbNotificaciones") != "" ){
			$strNotificaciones = GetParameter("cmbNotificaciones");
		}

		// Es un nuevo registro
		if ( $_POST["action"] == "new" && $MsgBoxMessage == "" )
		{
				// Verifico que no existe ese mail ya registrado
				$strSQLCommVerifico = " SELECT * " .
						  " FROM usuario " .
						  " WHERE LCase(usuario_email) = '" . strtolower($strEmail) . "'" . 
						  " AND activo = 'Y'";
						  
				$Result = mysql_query($strSQLCommVerifico);
				if(mysql_num_rows($Result)){
					if ( $oRs = mysql_fetch_array($Result) )
					{
						$MsgBoxMessage = "El e-mail ya existe. <br/> Verifique sus datos de acceso.";
					}
				}
				mysql_free_result($Result);
			
			
			
			if($MsgBoxMessage == ''){
				// Armo la sentencia de INSERT
				$strSQLComm = " INSERT INTO usuario( usuario_nombre, usuario_apellido, " . 
							" prov_id, tipo_usuario, usuario_email, usuario_password, tipodni_id, " . 
							" usuario_dni, usuario_ciudad, usuario_fechanac, usuario_telefono, usuario_celular, usuario_notificaciones, activo" .
								" ) " .
								" VALUES (" .
								"'" . SafeSql($strNombre) . "'," .
								"'" . SafeSql($strApellido) . "'," .
								SafeSql($iIdProvincia) . "," .
								"'" . SafeSql($strTipoUsuario) . "'," .
								"'" . SafeSql($strEmail) . "'," .
								"'" . SafeSql($strPassword) . "'," .
								SafeSql($iIdTipoDni) . "," .
								"'" . SafeSql($strDni) . "'," .
								"'" . SafeSql($strCiudad) . "'," .
								SqlDate($strFechaNac) . "," .
								"'" . SafeSql($strTelefono) . "'," .
								"'" . SafeSql($strCelular) . "'," .
								"'" . SafeSql($strNotificaciones) . "'," .
								" 'P'" .
								")";
				mysql_query($strSQLComm);
			
				// Obtengo si ocurrio algun error
				if(mysql_error() != ""){
					$MsgBoxMessage = "Hubo problemas en el envío de datos, intentelo nuevamente o pongase en contacto con nosotros.";
				}
								
			}
			
			
			// Si No hay error, direcciono al BROWSE
			if ( $MsgBoxMessage == "" )
			{
				$MsgBoxMessage = "Sus datos han sido registrados correctamente. <br> Ya forma parte de VirtualU!. Para cerrar el proceso de registración se le ha enviado un mail de confirmación.";
				$strActionLogin = "";
				$iId = "";
					$Result = mysql_query("SELECT MAX(U.usuario_id) AS Id FROM usuario AS U");
					if ( $ObjRs = mysql_fetch_array($Result) )
					{
						$iId = $ObjRs["Id"];
					}
					/* Liberar conjunto de resultados */
					mysql_free_result($Result);
					
					/*$_SESSION["UserLogged"] = 1;
					$_SESSION["UserId"] = $iId;
					$_SESSION["UserName"] = $strEmail;
					$_SESSION["FirstName"] = $strNombre;
					$_SESSION["LastName"] = $strApellido;
					$_SESSION["UserAccess"] = $strTipoUsuario;*/
					
					$strNombreMensaje = "VirtualU";
					$strMail = $strEmail;
					$strTituloMensaje = "Proceso de Registración";
					$strMensaje = "Para cerrar la registración y empezar a trabajar en VirtualU ingrese <a href=" . $SITE_URL . "/micuenta.php?e=" . encrypt($iId) . ">aquí</a>.<br>Si no puede ingresar al link, copie y pegue en su navegador la siguiente url: <b>". $SITE_URL . "/micuenta.php?e=" . encrypt($iId) . "</b>";
					include "enviarmail.php";
				?><script language="javascript" type="text/javascript">EnviarMail('<?=$strNombreMensaje?>', '<?=$strMail?>', '<?=$strTituloMensaje?>', '<?=$strMensaje?>' );</script><?
			}
			else
			{
				$bHayError = TRUE;
			}
			include "common/inc_database_close.php";
			
		}
		else if ( $_POST["action"] == "edit" && GetParameter("id") != "" && $MsgBoxMessage == "" )
		{
		
 
			// ARMO LA SENTENCIA DE UPDATE
			$strSQLComm = " UPDATE usuario SET " .
						  	" usuario_nombre = '" . SafeSql($strNombre) . "'," .
							" usuario_apellido = '" . SafeSql($strApellido) . "'," .
							" prov_id = " . SafeSql($iIdProvincia) . "," .
							" tipo_usuario = '" . SafeSql($strTipoUsuario) . "'," .
							" tipodni_id = " . SafeSql($iIdTipoDni) . "," .
							" usuario_dni = '" . SafeSql($strDni) . "'," .
							" usuario_ciudad = '" . SafeSql($strCiudad) . "'," .
							" usuario_fechanac = " . SQLDate($strFechaNac) . "," .
							" usuario_telefono = '" . SafeSql($strTelefono) . "'," .
							" usuario_celular = '" . SafeSql($strCelular) . "'," .
							" usuario_notificaciones = '" . SafeSql($strNotificaciones) . "'" .
							" WHERE usuario_id = " . SafeSql($_POST["id"]);

		
			mysql_query($strSQLComm);
			
			// Obtengo si ocurriÃƒÂ³ algÃƒÂºn error
			$strMsqlError = mysql_error();
			
			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $strMsqlError == "" )
			{
				$MsgBoxMessage = "Sus datos han sido modificados correctamente.";
			
					$_SESSION["FirstName"] = $strNombre;
					$_SESSION["LastName"] = $strApellido;
					$_SESSION["UserAccess"] = $strTipoUsuario;
				//header("Location: $strReturnUrl");
				//exit();
			}
			else
			{
				$bHayError = TRUE;
			}
		
	}else{
		// Borro el registro
		if ( GetParameter("btnBorrar") != "" && GetParameter("id") )
		{
		/*
			include "common/inc_database_open.php";
			
			// Elimino el registro
			$strSQLComm = " DELETE FROM avisos " .
						  " WHERE avis_id = " . GetParameter("id");
			mysql_query($strSQLComm);
			
			// Obtengo si ocurriÃƒÂ³ algÃƒÂºn error
			$strMsqlError = mysql_error();
			
			// Elimino la relaciÃƒÂ³n
			if ( $strMsqlError == "" )
			{
				$strSQLComm = " DELETE FROM avisos_disciplinas " .
							  " WHERE avis_id = " . GetParameter("id");
				mysql_query($strSQLComm);
			}

			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $strMsqlError == "" )
			{
				//header("Location: $strReturnUrl");
				//exit();
			}
			else
			{
				$bHayError = TRUE;
			}
			
			*/
			
		}
	}
}	
	// FIN DE: Si se agrega un nuevo registro, realizo el alta, sino actualizo los datos

include "header.php";

include "common/inc_database_open.php";
		
		// Seteo variables que se mostrarÃƒÂ¡n en los INPUTS
		//$MsgBoxMessage = "";
		
		if ($strActionLogin == "edit" ) 
		{
			$BoxTitle = "Mis Datos";
			$iId = $_SESSION["UserId"];
			$strCommQuery = "SELECT U.*, DAY(U.usuario_fechanac) as dianac, MONTH(U.usuario_fechanac) AS mesnac, YEAR(U.usuario_fechanac) AS anionac, P.pais_id " .
									" FROM usuario AS U " . 
									" INNER JOIN provincias_estados AS PE ON PE.prov_id = U.prov_id " .
									" INNER JOIN pais AS P ON P.pais_id = PE.pais_id  " .
									" INNER JOIN tipodni AS TD ON TD.tipodni_id = U.tipodni_id " .
									" WHERE U.activo = 'Y' AND U.usuario_id = " . SafeSql($iId);
			$Result = mysql_query($strCommQuery);
			
			if ( $ObjRs = mysql_fetch_array($Result) )
			{
				$strNombre = $ObjRs["usuario_nombre"];
				$strApellido = $ObjRs["usuario_apellido"];
				$iIdPais = $ObjRs["pais_id"];
				
				$iIdProvincia = $ObjRs["prov_id"];
				
				$strTipoUsuario = $ObjRs["tipo_usuario"];
				
				$strEmail = $ObjRs["usuario_email"];
				$strPassword = $ObjRs["usuario_password"];
				$iIdTipoDNI = $ObjRs["tipodni_id"];
				
				$strUsuarioDNI = $ObjRs["usuario_dni"];
				$strCiudad = $ObjRs["usuario_ciudad"];
				$strDiaNac= $ObjRs["dianac"];
				$strMesNac= $ObjRs["mesnac"];
				$strAnioNac= $ObjRs["anionac"];
				
				$strTelefono = $ObjRs["usuario_telefono"];
				$strCelular = $ObjRs["usuario_celular"];
				$strNotificaciones = $ObjRs["usuario_notificaciones"];
				$strActivo = $ObjRs["activo"];
					
			}
			else
			{
			
				$MsgBoxMessage = "No existe ningun usuario con el mail indicado.";
			}
			
			/* Liberar conjunto de resultados */
			mysql_free_result($Result);

		}else if($strActionLogin == "new")
		{
			// Es un nuevo registro
			$BoxTitle = "Registracion de Nuevo Usuario";
					
				$strNombre = "";
				$strApellido = "";
				$iIdPais = "0";
				$iIdProvincia = "0";
				
				$strTipoUsuario = "";
				
				$strEmail = "";
				$strPassword = "";
				$iIdTipoDNI = "";
				
				$strUsuarioDNI = "";
				$strCiudad = "";
				$strDiaNac= "";
				$strMesNac= "";
				$strAnioNac= "";
				$strTelefono = "";
				$strCelular = "";
				$strNotificaciones = "";
				$strActivo = "";
			
		}
		// FIN DE: Seteo variables que se mostrarÃƒÂ¡n en los INPUTS
	
	//Obtengo Listado de Paises
	$strSQLCommPaises = " SELECT pais_id, pais_titulo " .
				  " FROM pais " .
				  " WHERE activo = 'Y' ORDER BY pais_titulo  ";
	$Result2 = mysql_query($strSQLCommPaises);
	
	//Obtengo Listado de Tipos de DNI
	$strSQLCommTipoDni = " SELECT tipodni_id, tipodni_titulo " .
				  " FROM tipodni " .
				  " ORDER BY tipodni_id ";
	$Result3 = mysql_query($strSQLCommTipoDni);
	

?>
<h2><?=$BoxTitle?></h2>
<? if($MsgBoxMessage != ''){ ?>
	<table border="0" align="center"><tr><td align="center"><p style="margin-left:30px; font-size:11px; font-weight:bold;"><?=$MsgBoxMessage?></p></td></tr>
	<tr><td align="center"><!--<input class="boton" type="button" value="Volver" onclick="history.back();" name="btnVolver" />--></td></tr></table>
<? }else{ ?>
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
		var iIdProvincia = <?=$iIdProvincia?>;   
		
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

function validarEmail(email)
{
    var splitted = email.match("^(.+)@(.+)$");
    if (splitted == null) return false;
    if (splitted[1] != null)
    {
        var regexp_user = /^\"?[\w-_\.]*\"?$/;
        if (splitted[1].match(regexp_user) == null) return false;
    }
    if (splitted[2] != null)
    {
        var regexp_domain = /^[\w-\.]*\.[A-Za-z]{2,4}$/;
        if (splitted[2].match(regexp_domain) == null)
        {
            var regexp_ip = /^\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\]$/;
            if (splitted[2].match(regexp_ip) == null) return false;
        } // if
        return true;
    }
    return false;
}       
       
       
function validarForm(thisForm) {
       
if(thisForm.txtEmail.value == ''){
	alert('Debe ingresar un email.');
	thisForm.txtEmail.focus();
	return false;
}else{

	if(!validarEmail(thisForm.txtEmail.value)){
		alert('Debe ingresar un email válido.');
		thisForm.txtEmail.focus();
		return false;
	}
   
}
if(thisForm.txtPassword.value == '' || thisForm.txtPassword.value.length < 6  || thisForm.txtPassword.value.length > 20 ){
	alert('Debe seleccionar una clave, la cual debe tener como mínimo 6 caracteres y máximo 20.');
	thisForm.txtPassword.focus();
	return false;
}
if(thisForm.cmbTipoDni.value == ''){
	alert('Debe seleccionar su tipo de dni.');
	thisForm.cmbTipoDni.focus();
	return false;
}
if(thisForm.txtNombre.value == ''){
	alert('Debe ingresar su nombre.');
	thisForm.txtNombre.focus();
	return false;
}
if(thisForm.txtApellido.value == ''){
	alert('Debe ingresar su apellido.');
	thisForm.txtApellido.focus();
	return false;
}
if(thisForm.fecha_1_1.value == ''){
	alert('Debe ingresar su fecha de nacimiento.');
	thisForm.fecha_1_1.focus();
	return false;
}
if(thisForm.txtDni.value == '' || isNaN(thisForm.txtDni.value)){
	alert('Debe ingresar su dni (sólo dígitos numéricos).');
	thisForm.txtDni.focus();
	return false;
}
if(thisForm.cmbPais.value == ''){
	alert('Debe seleccionar un país.');
	thisForm.cmbPais.focus();
	return false;
}
if(thisForm.cmbProvincia.value == ''){
	alert('Debe seleccionar una provincia.');
	thisForm.cmbProvincia.focus();
	return false;
}

return true;
}

</script>

<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm" onsubmit="return validarForm(this);">

	<input type="hidden" name="id" value="<?=$_SESSION["UserId"]?>">
	<input type="hidden" name="idu" value="<?=$iIdUser?>">
	<input type="hidden" name="action" value="<?=$strActionLogin?>">
	<input type="hidden" id="HidFecha" name="HidFecha">
	<input type="hidden" name="return_url" value="<?=GetParameter("return_url")?>" />
	<!-- FORMULARIO DE EDICION -->
	<div class="Formulario">
		<div class="separaModulo">
			<div class="AcomodaLabel">
				E-mail: (*)
			</div>
			<input type="text" name="txtEmail" id="txtEmail" value="<?=$strEmail?>" class="AcomodaTextBox" maxlength="40" 
			<? if($strActionLogin == "edit") { ?>  readonly="readonly"  <? } ?>  />
		</div>
		<div class="separaModulo">		
			<? if($strActionLogin != "edit") { ?>
			<div class="AcomodaLabel">
				Clave: (*)
			</div>
			<input type="password" name="txtPassword" id="txtPassword" value="<?=$strPassword?>" class="AcomodaTextBox" maxlength="20" />
			<? 	} ?>
		</div>
	</div>
		
	<h3> Información Personal </h3>
	<div class="Formulario">
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Nombre: (*)
			</div>
			<input type="text" name="txtNombre" value="<?=$strNombre?>" class="AcomodaTextBox" maxlength="50" />
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Apellido: (*)
			</div>
			<input type="text" name="txtApellido" value="<?=$strApellido?>" class="AcomodaTextBox" maxlength="50" />
		</div>
		<div class="separaModulo">
        	<div class="AcomodaLabel">
				Fecha de Nacimiento: (*)
			</div>
			<input id="fecha_1_1" class="AcomodaMiniTextBox" name="fecha_1_1" size="1" maxlength="2" value="<?=$strDiaNac?>" type="text" readonly="readonly" /><label for="fecha_1_1"></label>
			<input id="fecha_1_2" class="AcomodaMiniTextBox" name="fecha_1_2" size="1" maxlength="2" value="<?=$strMesNac?>" type="text" readonly="readonly" /><label for="fecha_1_2"></label>
			<input id="fecha_1_3" class="AcomodaMiniTextBox" name="fecha_1_3" size="3" maxlength="4" value="<?=$strAnioNac?>" type="text" style="width: 40px;" readonly="readonly" /><label for="fecha_1_3"></label>
			
			<span id="calendar_2"><img id="cal_img_2" src="images/calendar.gif" alt="Elige una fecha." class="AcomodaCalendario" ></span>
			<script type="text/javascript">
				Calendar.setup({
				inputField	 : "fecha_1_3",
				baseField    : "fecha_1",
				displayArea  : "calendar_2",
				button		 : "cal_img_2",
				ifFormat	 : "%d/%m/%Y",  
				onSelect	 : selectEuropeDate
				});
			</script>
        </div>
		<div class="separaModulo">
        	<div class="AcomodaLabel">
				Documento: (*)
			</div>
            <select class="AcomodaMiniDdl" name="cmbTipoDni" id="cmbTipoDni">
				<?
				if(mysql_num_rows($Result3)){
					while($oRs = mysql_fetch_array($Result3))
					{?>
						<option value="<?=$oRs["tipodni_id"]?>"<? if($oRs["tipodni_id"] == trim("".$iIdTipoDNI)){?> selected="selected"<? }?>><?=$oRs["tipodni_titulo"]?></option>
					<?
					}
				}
				mysql_free_result($Result3);
				?>
			</select>
			<input type="text" name="txtDni" value="<?=$strUsuarioDNI?>" class="AcomodaTextBoxDdl" maxlength="12" style="width: 90px;" />
			   &nbsp;&nbsp;<small>&nbsp; ( ingrese solo dígitos ) </small>
        </div>
		<div class="separaModulo">
        	<div class="AcomodaLabel">
				Pa&iacute;s:
			</div>
            <select class="AcomodaDdl" name="cmbPais" id="cmbPais" onchange="TraeProvincias();">
				<option value="">--Seleccionar--</option>
				<?
				if(mysql_num_rows($Result2)){
					while($oRs = mysql_fetch_array($Result2))
					{?>
						<option value="<?=$oRs["pais_id"]?>"<? if (trim("".$oRs["pais_id"]) == trim("".$iIdPais)){?> selected="selected"<? }?>><?=$oRs["pais_titulo"]?></option>
					<?
					}
				}
				mysql_free_result($Result2);
				?>
			</select>
        </div>
		<div class="separaModulo">
        	<div class="AcomodaLabel">
				Provincia:
			</div>
			<div id="dvProvincia">
			<select name="cmbProvincia" id="cmbProvincia" class="AcomodaDdl">
					<option value="">--Seleccionar--</option>
			</select>
			</div>
        </div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Ciudad:
			</div>
            <input type="text" name="txtCiudad" id="txtCiudad" value="<?=$strCiudad?>" class="AcomodaTextBox" maxlength="100" />
		</div>
        <div class="separaModulo">
			<div class="AcomodaLabel">
				Tel&eacute;fono:
			</div>
			<input type="text" name="txtTelefono" value="<?=$strTelefono?>" class="AcomodaTextBox" maxlength="50" />
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Celular:
			</div>
			<input type="text" name="txtCelular" value="<?=$strCelular?>" class="AcomodaTextBox" maxlength="50" />
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Perfil:
			</div>
			<select name="cmbTipoUsuario" class="AcomodaDdl">
				<option value="L" <? if ($strTipoUsuario == "L") { ?>selected<? } ?> >Alumno</option>
				<option value="Y" <? if ($strTipoUsuario == "Y") { ?>selected<? } ?> >Ayudante</option>
				<option value="P" <? if ($strTipoUsuario == "A") { ?>selected<? } ?> >Profesor</option>
			</select>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Recibir notificaciones?:
			</div>
			<select name="cmbNotificaciones" class="AcomodaDdl">
				<option value="Y" <? if ($strNotificaciones == "Y") { ?>selected<? } ?> >Si</option>
				<option value="N" <? if ($strNotificaciones == "N") { ?>selected<? } ?> >No</option>
			</select>
		</div>
	</div>
	<div class="PosicionBotones">
		<input class="boton" type="submit" name="btnActualizar" value="Enviar" />
		<input class="boton" type="button" value="Volver" onclick="self.location='<?=$strReturnUrl?>';" name="btnVolver" />
	</div>
	
	<!-- FIN DE: FORMULARIO DE EDICION -->
</form>

<? }?>
<?
include "common/inc_database_close.php";
include "footer.php";
?>
<script language="JavaScript" type="text/javascript">

window.onload=function(){
	TraeProvincias();
}
</script>
