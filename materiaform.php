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

<script type="text/javascript" src="lib/jquery-1.3.2.min.js"></script>
<body>
<div id="contPopUp">
<?

$strReturnUrl = "mismaterias.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];
	
	if ( GetParameter("btnActualizar") != "" ) 
	{
		if ( GetParameter("action") == "new" && $MsgBoxMessage == "" )
		{
			include "common/inc_database_open.php";
			
			// Armo la sentencia de INSERT
			$strSQLComm = " INSERT INTO materia(materia_titulo, carrera_id, activo) " .
							" VALUES (" .
							"'" . SafeSql(GetParameter("txtMateria")) . "'," . 
							SafeSql(GetParameter("cmbCarrera")) . "," .
							" 'Y'" .
							")";
			mysql_query($strSQLComm);

			// Obtengo si ocurrió algún error
			if(mysql_error() != ""){
				//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
				$MsgBoxMessage = "No se pudo crear la materia, por favor inténtelo nuevamente.";
			}
			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $MsgBoxMessage == "" )
			{
				?><script language="javascript" type="text/javascript">CerrarPopup();</script><?
				exit();
			}
			
		}
	}
	// FIN DE: Si se agrega un nuevo registro

include "common/inc_database_open.php";
?>
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
		if(thisForm.txtMateria.value == ''){
			alert('Debe ingresar la materia.');
			thisForm.txtMateria.focus();
			return false;
		}
		
		
		return true;
	}
</script>
<h2>Nueva Materia</h2>
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
	<div class="FormularioPopUp">
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
			<div id="dvCarreras"><? ObtenerComboCarrera("", "", "0", "", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				T&iacute;tulo de la materia: (*)
			</div>
			<input type="text" id="txtMateria" name="txtMateria" value="<?=GetParameter("txtMateria")?>" maxlength="255" style="width:254px;"/>
		</div>
	</div>
	<div class="PosicionBotonesPopUp">
		<input class="boton" type="submit" name="btnActualizar" value="Crear">
		<input class="boton" type="button" value="Volver" onclick="CerrarPopup();" name="btnVolver"><br/><br/>

	</div>
	<!-- FIN DE: FORMULARIO DE EDICION -->
</form>
<?
include "common/inc_database_close.php";
?>

</div>

</body>
</html>
<script language="javascript" type="text/javascript">
	window.onload=function(){
		<? if(GetParameter("idu") != ""){?>
			TraerFacultades('<?=decrypt(GetParameter("idf"))?>', '--Seleccione--', 'N');
		<? }
		 if(GetParameter("idf") != ""){?>
			TraerCarreras('<?=decrypt(GetParameter("idr"))?>', '--Seleccione--', 'N');
		<? }?>
	}
</script>