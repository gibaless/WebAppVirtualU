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

$strReturnUrl = "mismaterias.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];
	
	if ( GetParameter("btnActualizar") != "" ) 
	{
		$strFechaDesde = "NULL";
		if(GetParameter("fecha_1_1") != ""){
			$strFechaDesde = SqlDate(GetParameter('fecha_1_1') ."/". GetParameter('fecha_1_2') . "/". GetParameter('fecha_1_3'));
		}
		$strFechaHasta = "NULL";
		if(GetParameter("fecha_2_1") != ""){
			$strFechaHasta = SqlDate(GetParameter('fecha_2_1') ."/". GetParameter('fecha_2_2') . "/". GetParameter('fecha_2_3'));
		}
		
		if ( GetParameter("action") == "new" && $MsgBoxMessage == "" )
		{
			include "common/inc_database_open.php";
			
			// Armo la sentencia de INSERT
			$strSQLComm = " INSERT INTO comision(comision_titulo, comision_codigo, materia_id, comision_fechadesde, comision_fechahasta, activo) " .
							" VALUES (" .
							"'" . SafeSql(GetParameter("txtComision")) . "'," . 
							"'" . SafeSql(GetParameter("txtCodigo")) . "'," . 
							SafeSql(GetParameter("cmbMateria")) . "," .
							$strFechaDesde . "," .
							$strFechaHasta . "," .
							" 'Y'" .
							")";
			mysql_query($strSQLComm);

			// Obtengo si ocurrió algún error
			if(mysql_error() != ""){
				//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
				$MsgBoxMessage = "No se pudo crear la comisión, por favor inténtelo nuevamente.";
			}else{
				$iId = "";
				$Result = mysql_query("SELECT MAX(comision_id) AS Id FROM comision");
				if ( $ObjRs = mysql_fetch_array($Result) )
				{
					$iId = $ObjRs["Id"];
				}
				/* Liberar conjunto de resultados */
				mysql_free_result($Result);

				// Armo la sentencia de INSERT de la relación
				$strSQLComm = " INSERT INTO usuario_comision(usuario_id, comision_id, usuario_tipo, activo) " .
								" VALUES (" .
								SafeSql($_SESSION["UserId"]) . "," . 
								SafeSql($iId) . "," .
								" 'P'," .
								" 'Y'" .
								")";
				mysql_query($strSQLComm);
	
				// Obtengo si ocurrió algún error
				if(mysql_error() != ""){
					//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
					$MsgBoxMessage = "No se pudo inscribir en la comisión, por favor inténtelo nuevamente.";
				}
			
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
?>
<script language="JavaScript" type="text/javascript">
	function IngresarFacultad(){
		window.open('facultadform.php?action=new','facultad','toolbar=no,menubar=no,resizable=no,scrollbars=no,width=480,height=200, left=100,top=100');
	}
	
	function IngresarCarrera(){
		window.open('carreraform.php?action=new','carrera','toolbar=no,menubar=no,resizable=no,scrollbars=yes,width=490,height=210, left=100, top=100');
	}
	
	function IngresarMateria(){
		window.open('materiaform.php?action=new','materia','toolbar=no,menubar=no,resizable=no,scrollbars=yes,width=490,height=236, left=100, top=100');
	}
	
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
		if(thisForm.txtComision.value == ''){
			alert('Debe ingresar la comision.');
			thisForm.txtComision.focus();
			return false;
		}
		if(thisForm.fecha_1_1.value == ''){
			alert('Debe ingresar la fecha desde.');
			thisForm.fecha_1_1.focus();
			return false;
		}
		if(thisForm.fecha_2_1.value == ''){
			alert('Debe ingresar la fecha hasta.');
			thisForm.fecha_2_1.focus();
			return false;
		}
		
		return true;
	}
</script>
<h2>Alta de Comisión</h2>
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
			<div class="dvUniversidades"><? ObtenerComboUniversidad("", decrypt(GetParameter("idu")), "onchange=\"TraerFacultades('" . decrypt(GetParameter("idf")) . "','--Seleccione--','N');\"", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div>
			<small class="labelSmall">si no encuentra la universidad ingr&eacute;sela <a href="universidadform.php?action=new">aqu&iacute;</a></small>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Facultad: (*)
			</div>
			<div id="dvFacultades"><? ObtenerComboFacultad("", "", "0", "onchange=\"TraerCarreras('" . decrypt(GetParameter("idr")) . "','--Seleccione--','N');\"", "--Seleccione--", "Y",  "AcomodaDdlFiltroGrande")?></div>
			<small class="labelSmall">si no encuentra la facultad ingr&eacute;sela <a href="javascript://;" onclick="IngresarFacultad();">aqu&iacute;</a></small>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Carrera: (*)
			</div> 
			<div id="dvCarreras"><? ObtenerComboCarrera("", "", "0", "onchange=\"TraerMaterias('" . decrypt(GetParameter("idm")) . "','--Seleccione--','N');\"", "--Seleccione--", "Y",  "AcomodaDdlFiltroGrande")?></div>
			<small class="labelSmall">si no encuentra la carrera ingr&eacute;sela <a href="javascript://;" onclick="IngresarCarrera();">aqu&iacute;</a></small>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Materia: (*)
			</div>
			<td><div id="dvMaterias"><? ObtenerComboMateria("", "", "0", "", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div>
			<small class="labelSmall">si no encuentra la materia ingr&eacute;sela <a href="javascript://;" onclick="IngresarMateria();">aqu&iacute;</a></small>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				T&iacute;tulo de la comisi&oacute;n: (*)
			</div>
			<input type="text" id="txtComision" name="txtComision" value="<?=GetParameter("txtComision")?>" maxlength="100" class="AcomodaTextBox" />
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				C&oacute;digo:
			</div>
			<input type="text" id="txtCodigo" name="txtCodigo" value="<?=GetParameter("txtCodigo")?>" maxlength="15" class="AcomodaTextBox" />
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Fecha desde: (*)
			</div>
			<span><input class="AcomodaMiniTextBox" id="fecha_1_1" name="fecha_1_1" readonly="true" class="element text" size="1"  maxlength="2" value="<?=GetParameter("fecha_1_1")?>" type="text"><label for="fecha_1_1"></label></span>
			<span><input class="AcomodaMiniTextBox" id="fecha_1_2" name="fecha_1_2" readonly="true" class="element text" size="1" maxlength="2" value="<?=GetParameter("fecha_1_2")?>" type="text"><label for="fecha_1_2"></label></span>
			<span><input class="AcomodaMiniTextBox" id="fecha_1_3" name="fecha_1_3" readonly="true" class="element text" size="3"  maxlength="4" value="<?=GetParameter("fecha_1_3")?>" type="text" style="width: 40px;"><label for="fecha_1_3"></label></span>
			
			<span id="calendar_1"><img id="cal_img_1" class="datepicker" src="images/calendar.gif" alt="Elige una fecha." class="AcomodaCalendario" ></span>
			<script type="text/javascript">
				Calendar.setup({
				inputField	 : "fecha_1_3",
				baseField    : "fecha_1",
				displayArea  : "calendar_1",
				button		 : "cal_img_1",
				ifFormat	 : "%d/%m/%Y",  
				onSelect	 : selectEuropeDate
				});
			</script>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Fecha hasta: (*)
			</div>
			<span><input class="AcomodaMiniTextBox" id="fecha_2_1" name="fecha_2_1" readonly="true" class="element text" size="1"  maxlength="2" value="<?=GetParameter("fecha_2_1")?>" type="text"><label for="fecha_2_1"></label></span>
			<span><input class="AcomodaMiniTextBox" id="fecha_2_2" name="fecha_2_2" readonly="true" class="element text" size="1" maxlength="2" value="<?=GetParameter("fecha_2_2")?>" type="text"><label for="fecha_2_2"></label></span>
			<span><input class="AcomodaMiniTextBox" id="fecha_2_3" name="fecha_2_3" readonly="true" class="element text" size="3"  maxlength="4" value="<?=GetParameter("fecha_2_3")?>" type="text" style="width: 40px;"><label for="fecha_2_3"></label></span>
			
			<span id="calendar_2"><img id="cal_img_2" class="datepicker" src="images/calendar.gif" alt="Elige una fecha." class="AcomodaCalendario"></span>
			<script type="text/javascript">
				Calendar.setup({
				inputField	 : "fecha_2_3",
				baseField    : "fecha_2",
				displayArea  : "calendar_2",
				button		 : "cal_img_2",
				ifFormat	 : "%d/%m/%Y",  
				onSelect	 : selectEuropeDate
				});
			</script>
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
<script language="javascript" type="text/javascript">
	window.onload=function(){
		<? if(GetParameter("idu") != ""){?>
			TraerFacultades('<?=decrypt(GetParameter("idf"))?>', '--Seleccione--', 'N');
		<? }else{?>
				document.getElementById("cmbUniversidad").selectedIndex = 0;
		<? }
		 if(GetParameter("idf") != ""){?>
			TraerCarreras('<?=decrypt(GetParameter("idr"))?>', '--Seleccione--', 'N');
		<? }else{?>
				document.getElementById("cmbFacultad").selectedIndex = 0;
		<? }
		 if(GetParameter("idr") != ""){?>
			TraerMaterias('<?=decrypt(GetParameter("idm"))?>', '--Seleccione--', 'N');
		<? }else{?>
				document.getElementById("cmbCarrera").selectedIndex = 0;
		<? }?>
		document.getElementById("cmbMateria").selectedIndex = 0;
	}
</script>