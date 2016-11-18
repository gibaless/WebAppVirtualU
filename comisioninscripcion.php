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
$strMensajeOK = "";
$MsgBoxMessage = "";
$BoxWidth = "";
$BoxEdit = "Off";
$BoxHelp = "Off";
$current0 = "On";

$strFile = "";

// Defino variable de acceso para el INCLUDE: Access
$RequireAccess = "A|L|P|Y";

include "access.php";
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
$bHayError = FALSE;

$strReturnUrl = "mismaterias.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];
	
	if ( GetParameter("btnActualizar") != "" ) 
	{
		$strModalidad = "L";
		$strIntegrante = "alumno";
		if(GetParameter("cmbModalidad") != ""){
			$strModalidad = GetParameter("cmbModalidad");
			$strIntegrante = "ayudante/profesor adjunto";
		}
		
		$strNombreMensaje = "VirtualU";
		$strMail = "";
		$strTituloMensaje = "Nuevo " . $strIntegrante;
		$strMensaje = "Se ha inscripto un nuevo " . $strIntegrante . ". Para habilitarlo a la comisión ingrese <a href=" . $SITE_URL . "/comisiondetalle.php?id=" . encrypt(GetParameter("cmbComision")) . ">aquí</a>.<br>Si no puede ingresar al link, copie y pegue en su navegador la siguiente url: <b>". $SITE_URL . "/comisiondetalle.php?id=" . encrypt(GetParameter("cmbComision")) . "</b>";
		
		if ( $MsgBoxMessage == "" )
		{
			include "common/inc_database_open.php";
			
			// Armo la sentencia de INSERT
			$strSQLComm = " INSERT INTO usuario_comision(usuario_id, comision_id, usuario_tipo, activo) " .
							" VALUES (" .
							SafeSql($_SESSION["UserId"]) . "," . 
							SafeSql(GetParameter("cmbComision")) . "," .
							" '" . SafeSql($strModalidad) . "'," .
							" 'P'" .
							")";
			mysql_query($strSQLComm);
			
			// Obtengo si ocurrió algún error
			if(mysql_error() != ""){
				$strSQLComm = " SELECT usuario_id " . 
							  " FROM usuario_comision  " .
							  " WHERE activo = 'N' " . 
							  " AND comision_id = " . SafeSql(GetParameter("cmbComision")) .
							  " AND usuario_id = " . SafeSql($_SESSION["UserId"]);
				$Result = mysql_query($strSQLComm);
				if(mysql_num_rows($Result)){
					//Reiscribo al usuario
					$strSQLComm = " UPDATE usuario_comision SET " .
								  " usuario_tipo = '" . SafeSql($strModalidad) . "'," .
								  " activo = 'P' " . 
								  " WHERE comision_id = " . SafeSql(GetParameter("cmbComision")) .
								  " AND usuario_id = " . SafeSql($_SESSION["UserId"]);
					mysql_query($strSQLComm);
					$strMensajeOK = "Ya se ha imscripto en la comisión. Para empezar a trabajar en ella debe aguardar a ser aprobado por un profesor. ";
					$strMail = ObtenerMails(GetParameter("cmbComision"), "C", "'P','Y'");
				}else{
					$MsgBoxMessage = "No se pudo inscribir en la comisión, por favor verifique si ya no está inscripto o inténtelo nuevamente.";
				}
				mysql_free_result($Result);
			}else{
				$strMail = ObtenerMails(GetParameter("cmbComision"), "C", "'P','Y'");
				$strMensajeOK = "Ya se ha inscripto en la comisión. Para empezar a trabajar en ella debe aguardar a ser aprobado por un profesor. ";
			}
			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $MsgBoxMessage == "" )
			{
				include "enviarmail.php";
				?><script language="javascript" type="text/javascript">EnviarMail('<?=$strNombreMensaje?>', '<?=$strMail?>', '<?=$strTituloMensaje?>', '<?=$strMensaje?>' );</script><?
				//header("Location:" . $strReturnUrl);
				//exit();
			}
			
		}
	}
	// FIN DE: Si se agrega un nuevo registro
include "header.php";
include "common/inc_database_open.php";
?>
<h2>Inscripción a Materia</h2>
<? if($strMensajeOK != ""){?>
	<div align="center">
	<p style="margin-left:30px; font-size:11px;"><?=$strMensajeOK?></p>
	<br />
	<br />
	<input type="button" class="boton" onclick="self.location='<?=$strReturnUrl?>';" value="Volver"/>
	</div>
<? }else{?>
	<script language="JavaScript" type="text/javascript">
	function validarForm(thisForm) {
		
	
		if(thisForm.cmbUniversidad.value == ''){
			alert('Debe seleccionar una universidad.');
			thisForm.cmbUniversidad.focus();
			return false;
		}
		if(thisForm.cmbFacultad.value == ''){
			alert('Debe seleccionar una facultad.');
			thisForm.cmbFacultad.focus();
			return false;
		}
		if(thisForm.cmbCarrera.value == ''){
			alert('Debe seleccionar una carrera.');
			thisForm.cmbCarrera.focus();
			return false;
		}
		if(thisForm.cmbMateria.value == ''){
			alert('Debe seleccionar una materia.');
			thisForm.cmbMateria.focus();
			return false;
		}
		if(thisForm.cmbComision.value == ''){
			alert('Debe seleccionar una comision.');
			thisForm.cmbComision.focus();
			return false;
		}
		<? if($_SESSION["UserAccess"] == "Y" || $_SESSION["UserAccess"] == "P"){?>
			if(thisForm.cmbModalidad.value == ''){
				alert('Debe seleccionar una modalidad.');
				thisForm.cmbModalidad.focus();
				return false;
			}
		<? }?>
		return true;
	}
	</script>
	
	<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm" onsubmit="return validarForm(this);">
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
				<div id="dvUniversidades"><? ObtenerComboUniversidad("", decrypt(GetParameter("idu")), "onchange=\"TraerFacultades('" . decrypt(GetParameter("idf")) . "','--Seleccione--','N');\"", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div>
			</div>
			<div class="separaModulo">
				<div class="AcomodaLabel">
					Facultad: (*)
				</div>
				<div id="dvFacultades"><? ObtenerComboFacultad("", "", "0", "onchange=\"TraerCarreras('" . decrypt(GetParameter("idr")) . "','--Seleccione--','N');\"", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div>
			</div>
			<div class="separaModulo">
				<div class="AcomodaLabel">
					Carrera: (*)
				</div>
				<div id="dvCarreras"><? ObtenerComboCarrera("", "", "0", "onchange=\"TraerMaterias('" . decrypt(GetParameter("idm")) . "','--Seleccione--','N');\"", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div>
			</div>
			<div class="separaModulo">
				<div class="AcomodaLabel">
					Materia: (*)
				</div>
				<div id="dvMaterias"><? ObtenerComboMateria("", "", "0", "onchange=\"TraerComisiones('" . decrypt(GetParameter("idc")) . "','--Seleccione--','N');\"", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div>
			</div>
			<div class="separaModulo">
				<div class="AcomodaLabel">
					Comisi&oacute;n: (*)
				</div>
				<div id="dvComisiones"><? ObtenerComboComision("", "", "0", "", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div>
			</div>
			<?
			if($_SESSION["UserAccess"] == "Y" || $_SESSION["UserAccess"] == "P"){?>
				<div class="separaModulo">
					<div class="AcomodaLabel">
						Modalidad: (*)
					</div>
					<select name="cmbModalidad" class="AcomodaDdlFiltroGrande">
						<option value="">--Seleccionar--</option>
						<option value="L">Alumno</option>
						<option value="Y">Ayudante / Profesor Adjunto</option>
					</select>
				</div>
			<?
			}?>
		</div>
		<div class="PosicionBotones">
			<input class="boton" type="submit" name="btnActualizar" value="Inscribirse">
			<input class="boton" type="button" value="Volver" onclick="self.location='<?=$strReturnUrl?>';" name="btnVolver">
		</div>
		<!-- FIN DE: FORMULARIO DE EDICION -->
	</form>
<?
}
include "common/inc_database_close.php";
include "footer.php";
if($strMensajeOK == ""){?>
	<script language="javascript" type="text/javascript">
		window.onload=function(){
			<? if(GetParameter("idu") != ""){?>
				TraerFacultades('<?=decrypt(GetParameter("idf"))?>', '--Seleccione--', 'N');
			<? }
			 if(GetParameter("idf") != ""){?>
				TraerCarreras('<?=decrypt(GetParameter("idr"))?>', '--Seleccione--', 'N');
			<? }
			 if(GetParameter("idr") != ""){?>
				TraerMaterias('<?=decrypt(GetParameter("idm"))?>', '--Seleccione--', 'N');
			<? }
			 if(GetParameter("idm") != ""){?>
				TraerComisiones('<?=decrypt(GetParameter("idc"))?>', '--Seleccione--', 'N');
			<? }?>
		}
	</script>
<? }?>