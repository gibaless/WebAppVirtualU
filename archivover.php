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
$BoxTitle = "Error";

$strFile = "";

// Defino variable de acceso para el INCLUDE: Access
$RequireAccess = "A|L|P|Y";

include "access.php";
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
$bHayError = FALSE;

$strPathFisico = realpath("./" . $DIR_ARCHIVOS) . "\\";
$strReturnUrl = "misarchivos.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];
	
include "header.php";
include "common/inc_database_open.php";

		// Seteo variables que se mostrarán en los INPUTS
		//$MsgBoxMessage = "";
		$strGrupo = "";
		$strComision = "";
		$strMateria = "";
		$strCarrera = "";
		$strFacultad = "";
		$strUniversidad = "";
		$strFecha = "";
		$strTitulo = "";
		$strDesarrollo = "";
		$strArchivo = "";	
		$strCreado = "";
		if ( GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id"))) ) 
		{
			$BoxTitle = "Detalle del archivo";
			$iId = decrypt(GetParameter("id"));
			
			
				
			
			// Obtengo el archivo.
			$strSQLCommArchivos = " SELECT DISTINCT archivo_id, archivo_fechaalta, archivo_titulo, archivo_archivo, archivo_descripcion, archivo_tipo, usuario_id, usuario_nombre, usuario_apellido, " . 
							" grupo, comision, materia, carrera, facultad, universidad, grupo_id, comision_id, materia_id, carrera_id, facultad_id, universidad_id " .
							" FROM " .  
							" ((SELECT A.archivo_id, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
							" G.grupo_nombre AS grupo, C.comision_titulo AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, G.grupo_id AS grupo_id, C.comision_id AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id " .
							" FROM archivo AS A " . 
							" INNER JOIN usuario AS U ON U.usuario_id = A.usuario_id " . 
							" INNER JOIN archivo_grupo AS AG ON A.archivo_id = AG.archivo_id " . //Archivos de Grupos 
							" INNER JOIN grupo AS G ON (G.grupo_id = AG.grupo_id AND G.activo = 'Y') " . 
							" INNER JOIN usuario_grupo AS UG ON (UG.grupo_id = G.grupo_id AND UG.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " . 
							" INNER JOIN comision AS C ON (C.comision_id = G.comision_id AND C.activo = 'Y') " . 
							" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
							" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
							" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
							" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
							" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
							" WHERE A.activo = 'Y' " .
							" AND U.activo = 'Y' " .
							" )UNION( " .
							" SELECT A.archivo_id, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
							" 'TODOS' AS grupo, C.comision_titulo AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, C.comision_id AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id " .
							" FROM archivo AS A " . 
							" INNER JOIN usuario AS U ON U.usuario_id = A.usuario_id " . 
							" INNER JOIN archivo_comision AS AC ON A.archivo_id = AC.archivo_id " .  //Archivos de Comisiones
							" INNER JOIN comision AS C ON (C.comision_id = AC.comision_id AND C.activo = 'Y') " . 
							" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " . 
							" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
							" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
							" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
							" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
							" WHERE A.activo = 'Y' " .
							" AND U.activo = 'Y' " .
							" )UNION( " .
							" SELECT A.archivo_id, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
							" 'TODOS' AS grupo, 'TODAS' AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id " .
			
							" FROM archivo AS A " . 
							" INNER JOIN usuario AS U ON U.usuario_id = A.usuario_id " . 
							" INNER JOIN archivo_materia AS AM ON A.archivo_id = AM.archivo_id " .  //Archivos de Materias
							" INNER JOIN comision AS C ON (C.materia_id = AM.materia_id AND C.activo = 'Y') " . 
							" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
							" INNER JOIN materia AS MA ON (MA.materia_id = MA.materia_id AND MA.activo = 'Y') " . 
							" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
							" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
							" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
							" WHERE A.activo = 'Y' " .
							" AND U.activo = 'Y' " .
							" )UNION( " .
							" SELECT A.archivo_id, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
							" 'TODAS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id " .
							" FROM archivo AS A " . 
							" INNER JOIN usuario AS U ON U.usuario_id = A.usuario_id " . 
							" INNER JOIN archivo_carrera AS AR ON A.archivo_id = AR.archivo_id " .  //Archivos de Carreras
							" INNER JOIN materia AS MA ON (MA.carrera_id = AR.carrera_id AND MA.activo = 'Y') " . 
							" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
							" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
							" INNER JOIN carrera AS R ON (R.carrera_id = AR.carrera_id AND R.activo = 'Y') " . 
							" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
							" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
							" WHERE A.activo = 'Y' " .
							" AND U.activo = 'Y' " .
							" )UNION( " .
							" SELECT A.archivo_id, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
							" 'TODOS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, 'TODAS' AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, 0 AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id " .
							" FROM archivo AS A " . 
							" INNER JOIN usuario AS U ON U.usuario_id = A.usuario_id " . 
							" INNER JOIN archivo_facultad AS AF ON A.archivo_id = AF.archivo_id " .  //Archivos de Facultades
							" INNER JOIN carrera AS R ON (R.facultad_id = AF.facultad_id AND R.activo = 'Y') " . 
							" INNER JOIN materia AS MA ON (MA.carrera_id = R.carrera_id AND MA.activo = 'Y') " . 
							" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
							" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
							" INNER JOIN facultad AS F ON (F.facultad_id = AF.facultad_id AND F.activo = 'Y') " . 
							" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
							" WHERE A.activo = 'Y' " .
							" AND U.activo = 'Y' " .
							" )UNION( " .
							" SELECT A.archivo_id, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
							" 'TODOS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, 'TODAS' AS carrera, 'TODAS' AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, 0 AS carrera_id, 0 AS facultad_id, UN.universidad_id AS universidad_id " .
							" FROM archivo AS A " . 
							" INNER JOIN usuario AS U ON U.usuario_id = A.usuario_id " . 
							" INNER JOIN archivo_universidad AS AU ON A.archivo_id = AU.archivo_id " .  //Archivos de Universidades
							" INNER JOIN facultad AS F ON (F.universidad_id = AU.universidad_id AND F.activo = 'Y') " . 
							" INNER JOIN carrera AS R ON (R.facultad_id = F.facultad_id AND R.activo = 'Y') " . 
							" INNER JOIN materia AS MA ON (MA.carrera_id = R.carrera_id AND MA.activo = 'Y') " . 
							" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
							" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
							" INNER JOIN universidad AS UN ON (UN.universidad_id = AU.universidad_id AND UN.activo = 'Y') " . 
							" WHERE A.activo = 'Y' " .
							" AND U.activo = 'Y' " .
							" )) AS CTOTAL " .
							" WHERE 1 = 1 " . 
							" AND CTOTAL.archivo_id = " . SafeSql($iId);
			
			$Result = mysql_query($strSQLCommArchivos);
			
			if ( $oRs = mysql_fetch_array($Result) )
			{
				$iIdArchivo = $oRs["archivo_id"];
				$strGrupo = $oRs["grupo"];
				$strComision = $oRs["comision"];
				$strMateria = $oRs["materia"];
				$strCarrera = $oRs["carrera"];
				$strFacultad = $oRs["facultad"];
				$strUniversidad = $oRs["universidad"];
				$strUsuario = $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"];
				$strFecha = DateTimeFormat($oRs["archivo_fechaalta"]);
				$strTitulo = $oRs["archivo_titulo"];
				$strDesarrollo = $oRs["archivo_descripcion"];
				$strArchivo = $oRs["archivo_archivo"];	
				$strCreado = DateTimeFormat($oRs["archivo_fechaalta"]) . " por <strong>" . $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"] . "</strong>";
									
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
		// FIN DE: Seteo variables que se mostrarán en los INPUTS
	
  

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
	<table border="0" cellpadding="2" cellspacing="2" align="center" style="font-size:12px;">
		<tr>
			<td align="right"><strong>Subido:</strong></td>
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
			<td align="right"><strong>Título:</strong></td>
			<td><?=$strTitulo?></td>
		</tr>
		<tr>
			<td valign="top" align="right"><strong>Descripción:</strong></td>
			<td><?=$strDesarrollo?></td>
		</tr>
		<tr>
			<td align="right"><strong>Archivo:</strong></td>
			<td>
				<a href="descarga.php?id=<?=encrypt($iIdArchivo)?>&t=A"><img src="images/download.png" title="Descargar" style="width:40px;"/></a>
			</td>
		</tr>
		
		<tr class="TableBtns">
			<td valign="bottom" align="center" colspan="2">
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