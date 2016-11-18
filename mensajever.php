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
$RequireAccess = "A|L|P|Y";

include "access.php";
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
$bHayError = FALSE;

$strReturnUrl = "mismensajes.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];
	
include "header.php";
include "common/inc_database_open.php";

$strBoxTitle = "No existe el mensaje";
$strUsuario = "";
$strFecha = "";
$strCreado = "";
$strEnviadoA = "";
$strTitulo = "";
$strMensaje = "";
$strTipo = "";
$strUniversidad = "";
$strFacultad = "";
$strCarrera = "";
$strMateria = "";
$strComision = "";
$strGrupo = "";
$strAction = "";
$strUrlAction = "";

if(GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id")))){
	//Obtengo el mensaje
	$strSQLCommMensajes = " SELECT mensaje_id, mensaje_fecha, mensaje_titulo, mensaje_mensaje, mensaje_tipo, usuario_id, usuario_nombre, usuario_apellido, " . 
					" grupo, comision, materia, carrera, facultad, universidad, grupo_id, comision_id, materia_id, carrera_id, facultad_id, universidad_id, usuarioenviado_nombre, usuarioenviado_apellido " .
					" FROM " .  
					" ((SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'PRIVADO' AS grupo, 'PRIVADO' AS comision, 'PRIVADO' AS materia, 'PRIVADO' AS carrera, 'PRIVADO' AS facultad, 'PRIVADO' AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, 0 AS carrera_id, 0 AS facultad_id, 0 AS universidad_id, UMU.usuario_nombre AS usuarioenviado_nombre, UMU.usuario_apellido AS usuarioenviado_apellido " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON (U.usuario_id = M.usuario_id "; 
	if(GetParameter("action") == "env"){
		$strSQLCommMensajes = $strSQLCommMensajes .	"AND U.usuario_id = " . SafeSql($_SESSION["UserId"]); 
	}
	$strSQLCommMensajes = $strSQLCommMensajes .	") " . 
						" INNER JOIN mensaje_usuario AS MU ON (M.mensaje_id = MU.mensaje_id  ";
	if(GetParameter("action") == "rec"){
		$strSQLCommMensajes = $strSQLCommMensajes .	"AND MU.usuario_id = " . SafeSql($_SESSION["UserId"]); 
	}
	$strSQLCommMensajes = $strSQLCommMensajes .	") " . 
					" INNER JOIN usuario AS UMU ON (UMU.usuario_id = MU.usuario_id) " . //Mensajes Privados. 
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" G.grupo_nombre AS grupo, C.comision_titulo AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, G.grupo_id AS grupo_id, C.comision_id AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_grupo AS MG ON M.mensaje_id = MG.mensaje_id " . //Mensajes de Grupos 
					" INNER JOIN grupo AS G ON (G.grupo_id = MG.grupo_id AND G.activo = 'Y') " . 
					" INNER JOIN usuario_grupo AS UG ON (UG.grupo_id = G.grupo_id AND UG.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " . 
					" INNER JOIN comision AS C ON (C.comision_id = G.comision_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
					" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
					" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
					" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
					" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'TODOS' AS grupo, C.comision_titulo AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, C.comision_id AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_comision AS MC ON M.mensaje_id = MC.mensaje_id " .  //Mensajes de Comisiones
					" INNER JOIN comision AS C ON (C.comision_id = MC.comision_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " . 
					" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
					" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
					" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
					" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
	
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'TODOS' AS grupo, 'TODAS' AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_materia AS MM ON M.mensaje_id = MM.mensaje_id " .  //Mensajes de Materias
					" INNER JOIN comision AS C ON (C.materia_id = MM.materia_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
					" INNER JOIN materia AS MA ON (MA.materia_id = MM.materia_id AND MA.activo = 'Y') " . 
					" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
					" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
					" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'TODAS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_carrera AS MR ON M.mensaje_id = MR.mensaje_id " .  //Mensajes de Carreras
					" INNER JOIN materia AS MA ON (MA.carrera_id = MR.carrera_id AND MA.activo = 'Y') " . 
					" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
					" INNER JOIN carrera AS R ON (R.carrera_id = MR.carrera_id AND R.activo = 'Y') " . 
					" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
					" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'TODOS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, 'TODAS' AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, 0 AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_facultad AS MF ON M.mensaje_id = MF.mensaje_id " .  //Mensajes de Facultades
					" INNER JOIN carrera AS R ON (R.facultad_id = MF.facultad_id AND R.activo = 'Y') " . 
					" INNER JOIN materia AS MA ON (MA.carrera_id = R.carrera_id AND MA.activo = 'Y') " . 
					" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
					" INNER JOIN facultad AS F ON (F.facultad_id = MF.facultad_id AND F.activo = 'Y') " . 
					" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'TODOS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, 'TODAS' AS carrera, 'TODAS' AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, 0 AS carrera_id, 0 AS facultad_id, UN.universidad_id AS universidad_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_universidad AS MU ON M.mensaje_id = MU.mensaje_id " .  //Mensajes de Universidades
					" INNER JOIN facultad AS F ON (F.universidad_id = MU.universidad_id AND F.activo = 'Y') " . 
					" INNER JOIN carrera AS R ON (R.facultad_id = F.facultad_id AND R.activo = 'Y') " . 
					" INNER JOIN materia AS MA ON (MA.carrera_id = R.carrera_id AND MA.activo = 'Y') " . 
					" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
					" INNER JOIN universidad AS UN ON (UN.universidad_id = MU.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
					" )) AS CTOTAL " .
					" WHERE CTOTAL.mensaje_id = " . SafeSql(decrypt(GetParameter("id")));
		$Result = mysql_query($strSQLCommMensajes);  
		if(mysql_num_rows($Result)){
			if($oRs = mysql_fetch_array($Result)){
				$strBoxTitle = "Detalle del Mensaje";
				$strUsuario = $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"];
				$strFecha = DateTimeFormat($oRs["mensaje_fecha"]);
				$strCreado = $strFecha . " por " . $strUsuario;
				$strEnviadoA = $oRs["usuarioenviado_nombre"] . " " . $oRs["usuarioenviado_apellido"];
				$strTitulo = $oRs["mensaje_titulo"];
				$strMensaje = str_replace(chr(13), "<br>",$oRs["mensaje_mensaje"]);
				$strTipo = $oRs["mensaje_tipo"];
				$strUniversidad = $oRs["universidad"];
				$strFacultad = $oRs["facultad"];
				$strCarrera = $oRs["carrera"];
				$strMateria = $oRs["materia"];
				$strComision = $oRs["comision"];
				$strGrupo = $oRs["grupo"];
				$strAction = "Reenviar";
				$strUrlAction = "mensajeform.php?id=" . encrypt($oRs["mensaje_id"]) . "&action=edit";
				if(GetParameter("action") == "rec"){
					$strAction = "Responder";
					$strUrlAction = "mensajeform.php?id=" . encrypt($oRs["mensaje_id"]) . "&action=new";
				}
			}
		}
		/* Liberar conjunto de resultados */
		mysql_free_result($Result);
}
?>
<h3><?=$strBoxTitle?></h3>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm" onsubmit="return validarForm(this);">
	<input type="hidden" id="HidFecha" name="HidFecha">
	<input type="hidden" name="id" value="<?=GetParameter("id")?>" />
	<input type="hidden" name="action" value="<?=GetParameter("action")?>" />
	<input type="hidden" name="return_url" value="<?=GetParameter("return_url")?>" />
	<!-- FORMULARIO DE EDICION -->
	<?
	// Si hay un error muestro mensaje y termino
	if ($MsgBoxMessage != "") 
	{?>
		<p style="margin-left:30px; font-size:11px;"><img src="images/errormsg.png" title="Error" style=" vertical-align:middle;"/>&nbsp;&nbsp;&nbsp;		
		<?=$MsgBoxMessage?></p>
	<? }?>
	<table border="0" cellpadding="2" cellspacing="2" align="center" style="font-size:12px;">
		<tr>
			<td align="right"><strong>Enviado:</strong></td>
			<td><?=$strCreado?></td>
		</tr>
		<tr>
			<td align="right"><strong>Universidad:</strong></td>
			<td><?=$strUniversidad?></td>
		</tr>
		<tr>
			<td align="right"><strong>Facultad:</strong></td>
			<td><?=$strFacultad?></td>
		</tr>
		<tr>
			<td align="right"><strong>Carrera:</strong></td>
			<td><?=$strCarrera?></td>
		</tr>
		<tr>
			<td align="right"><strong>Materia:</strong></td>
			<td><?=$strMateria?></td>
		</tr>
		<tr>
			<td align="right"><strong>Comisi&oacute;n:</strong></td>
			<td><?=$strComision?></td>
		</tr>
		<tr>
			<td align="right"><strong>Grupo:</strong></td>
			<td><?=$strGrupo?></td>
		</tr>
		<tr>
			<td align="right"><strong>Usuario:</strong></td>
			<td><?=$strEnviadoA?></td>
		</tr>
		<tr>
			<td align="right"><strong>Título:</strong></td>
			<td><?=$strTitulo?></td>
		</tr>
		<tr>
			<td valign="top" align="right"><strong>Mensaje:</strong></td>
			<td><?=$strMensaje?></td>
		</tr>
		<tr class="TableBtns">
			<td valign="bottom" align="center" colspan="2"><br/>
				<? if($strAction != ""){?><input class="boton" type="button" name="btnAction" value="<?=$strAction?>" onclick="self.location='<?=$strUrlAction?>&return_url=<?=urlencode($strReturnUrl)?>'"><? }?>
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
