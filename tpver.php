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
$BoxTitle = "ERROR";

$strFile = "";

// Defino variable de acceso para el INCLUDE: Access
$RequireAccess = "A|L|P|Y";

include "access.php";
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
$bHayError = FALSE;

$strPathFisico = realpath("./" . $DIR_TPS) . "\\";
$strReturnUrl = "mistps.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];
	
include "header.php";
include "common/inc_database_open.php";

		// Seteo variables que se mostrarán en los INPUTS
		$MsgBoxMessage = "";
		$strComision = "";
		$strMateria = "";
		$strCarrera = "";
		$strFacultad = "";
		$strUniversidad = "";
		$strFecha = "";
		$strFechaEntrega = "";
		$strTitulo = "";
		$strDesarrollo = "";
		$strArchivo = "";	
		$strCreado = "";
		$strTipoUsuario = "";	
		
		if ( GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id"))) ) 
		{
			$BoxTitle = "Detalle del trabajo práctico";
			$iId = decrypt(GetParameter("id"));
			
			// Obtengo el tp.
			$strSQLCommTPs = " SELECT DISTINCT TP.tp_id, TP.tp_titulo, TP.tp_descripcion, Tp.tp_fechaalta, TP.tp_fechaentrega, Tp.tp_archivo, UR.usuario_id, UR.usuario_nombre, UR.usuario_apellido, C.comision_fechadesde, C.comision_fechahasta, U.tipo_usuario, UC.usuario_tipo, " . 
							" C.comision_titulo AS comision, C.comision_codigo, M.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad " .
							" FROM trabajo_practico AS TP " . 
							" INNER JOIN usuario AS UR ON UR.usuario_id = TP.usuario_id " . 
							" INNER JOIN usuario_comision AS UC ON (TP.comision_id = UC.comision_id AND UC.activo = 'Y') " . 
							" INNER JOIN usuario AS U ON U.usuario_id = UC.usuario_id " . 
							" INNER JOIN comision AS C ON (C.comision_id = TP.comision_id AND C.activo = 'Y') " . 
							" INNER JOIN materia AS M ON (M.materia_id = C.materia_id AND M.activo = 'Y') " . 
							" INNER JOIN carrera AS R ON (R.carrera_id = M.carrera_id AND R.activo = 'Y') " . 
							" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
							" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
							" WHERE U.activo = 'Y' " .
							" AND TP.activo = 'Y' " .
							" AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . 
							" AND TP.tp_id = " . SafeSql($iId);
			
			$Result = mysql_query($strSQLCommTPs);
			
			if ( $oRs = mysql_fetch_array($Result) )
			{
				$iIdTP = $oRs["tp_id"];
				$strComision = $oRs["comision"];
				if($oRs["comision_codigo"] != ""){
					$strComision = $strComision . " (" . $oRs["comision_codigo"] . ")";
				}
				$strMateria = $oRs["materia"];
				$strCarrera = $oRs["carrera"];
				$strFacultad = $oRs["facultad"];
				$strUniversidad = $oRs["universidad"];
				$strUsuario = $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"];
				$strFecha = DateTimeFormat($oRs["tp_fechaalta"]);
				$strFechaEntrega = DateFormat($oRs["tp_fechaentrega"]);
				$strTitulo = $oRs["tp_titulo"];
				$strDesarrollo = $oRs["tp_descripcion"];
				$strArchivo = $oRs["tp_archivo"];	
				$strCreado = DateTimeFormat($oRs["tp_fechaalta"]) . " por <strong>" . $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"] . "</strong>";
				$strTipoUsuario = $oRs["usuario_tipo"];
									
			}
			else
			{
				$MsgBoxMessage = "Datos erroneos.";
			}
			
			/* Liberar conjunto de resultados */
			mysql_free_result($Result);

		}else{
			$MsgBoxMessage = "Datos erroneos.";
		}
		// FIN DE: Seteo variables que se mostrarán
	
  

?>
<h3><?=$BoxTitle?></h3>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" enctype="multipart/form-data" method="post" name="frmForm" onsubmit="return validarForm(this);">
	<input type="hidden" name="id" value="<?=GetParameter("id")?>">
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
	<table border="0" cellpadding="2" cellspacing="2" align="center" style="font-size: 12px;font-weight:bold;">
		<tr>
			<td align="right">Subido:</td>
			<td><?=$strCreado?></td>
		</tr>
		<tr>
			<td align="right">Universidad:</td>
			<td><?=$strUniversidad?></td>
		</tr>
		<tr>
			<td align="right">Facultad:</td>
			<td><?=$strFacultad?></td>
		</tr>
		<tr>
			<td align="right">Carrera:</td>
			<td><?=$strCarrera?></td>
		</tr>
		<tr>
			<td align="right">Materia:</td>
			<td><?=$strMateria?></td>
		</tr>
		<tr>
			<td align="right">Comisi&oacute;n:</td>
			<td><?=$strComision?></td>
		</tr>
		<tr>
        	<td align="right">Fecha de Entrega:</td>
            <td><?=$strFechaEntrega?></td>
        </tr>
		<tr>
			<td align="right">Título:</td>
			<td><?=$strTitulo?></td>
		</tr>
		<tr>
			<td valign="top" align="right">Descripción:</td>
			<td><?=$strDesarrollo?></td>
		</tr>
		<tr>
			<td align="right">Archivo:</td>
			<td>
				<a href="descarga.php?id=<?=encrypt($iId)?>&t=T"><img src="images/download.png" title="Descargar" alt="Descargar" style="width:36px;border:0;"></a>
			</td>
		</tr>
		
		<tr class="TableBtns">
			<td valign="bottom" align="center" colspan="2">
				<? if($strTipoUsuario == "P" || $strTipoUsuario == "Y"){?>
						<input class="boton" type="button" value="Ver Entregas" onclick="self.location='entregasverprofesor.php?idt=<?=encrypt($iId)?>&return_url=<?=urlencode($strReturnUrl)?>'">
				<? }else{?>
					<input class="boton" type="button" value="Ver Entregas" onclick="self.location='entregasver.php?idt=<?=encrypt($iId)?>&return_url=<?=urlencode($strReturnUrl)?>'">
					<input class="boton" type="button" value="Realizar Nueva Entrega" onclick="self.location='entregaform.php?idt=<?=encrypt($iId)?>&return_url=<?=urlencode($strReturnUrl)?>'">
				<? }?>
				<input class="boton" type="button" value="Volver" onclick="self.location='<?=$strReturnUrl?>';" name="btnVolver">
			</td>
		</tr>
	</table>
	<!-- FIN DE: FORMULARIO DE EDICION -->
</form>
<?
include "common/inc_database_close.php";
include "footer.php";
?>