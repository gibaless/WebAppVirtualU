<?
session_start();

/* INCLUYO ARCHIVO DE CONFIGURACIÓN. */
include "includes/sitesettings_inc.php";
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
include "common/func_pagination_www.inc";

header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
header("Expires: -100000"); //Expira página

$RequireAccess = "A|L|P|Y";
include "access.php";

$strComision = "ERROR! Materia Inexistente.";

$iIdComision = "0";
if(GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id")))){
	$iIdComision = decrypt(GetParameter("id"));
}

$strParams = "id=" . GetParameter("id");

// ARMO RETURN URL
$strReturnUrl = $_SERVER["SCRIPT_NAME"];
if(isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] != ""){
	$strReturnUrl .=  "?" . $_SERVER["QUERY_STRING"];
}else {
	$strReturnUrl .=  "?" . $strParams;
}
$strReturnUrl = urlencode($strReturnUrl);

include "common/inc_database_open.php";

// Obtengo la materia.
$strSQLComm = " SELECT C.comision_id, C.comision_fechadesde, C.comision_fechahasta, UC.usuario_tipo, C.comision_codigo, " . 
				" C.comision_titulo AS comision, M.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad " .
				" FROM usuario_comision AS UC " . 
				" INNER JOIN usuario AS U ON U.usuario_id = UC.usuario_id " . 
				" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
				" INNER JOIN materia AS M ON (M.materia_id = C.materia_id AND M.activo = 'Y') " . 
				" INNER JOIN carrera AS R ON (R.carrera_id = M.carrera_id AND R.activo = 'Y') " . 
				" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
				" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
				" WHERE U.activo = 'Y' " .
				" AND UC.activo = 'Y' " .
				" AND U.usuario_id = " . SafeSql($_SESSION["UserId"]) . 
				" AND C.comision_id = " . SafeSql($iIdComision);
$Result = mysql_query($strSQLComm);

$strComisionCodigo = "";
$strPeriodo = "";
$strMateria = "";
$strCarrera = "";
$strFacultad = "";
$strUniversidad = "";
$strTipoUsuario = "";

if(mysql_num_rows($Result)){
	if($oRs = mysql_fetch_array($Result)){
		$strPeriodo = DateFormat($oRs["comision_fechadesde"]) . " - " . DateFormat($oRs["comision_fechahasta"]);
		$strComision = $oRs["comision"];
		$strComisionCodigo = $oRs["comision_codigo"];
		$strMateria = $oRs["materia"];
		$strCarrera = $oRs["carrera"];
		$strFacultad = $oRs["facultad"];
		$strUniversidad = $oRs["universidad"];
		$strTipoUsuario = $oRs["usuario_tipo"];
		switch($strTipoUsuario){
			case "L":
				$strTipoUsuario = "Alumno";
				break;
			case "Y":
				$strTipoUsuario = "Ayudante";
				break;
			case "P":
				$strTipoUsuario = "Profesor";
				break;
			default:
				$strTipoUsuario = "";
				break;
		}
		$strComision = $strUniversidad . " - " . $strFacultad . " (" . $strCarrera . ") " . $strMateria . " - " . $strComision;
		
			
	}
}else{
	$iIdComision = "0";
}
/* Liberar conjunto de resultados */
mysql_free_result($Result);


// Obtengo el listado de grupos.
$strSQLCommGrupos = " SELECT DISTINCT G.grupo_id, G.grupo_nombre, U.usuario_nombre, U.usuario_apellido " .
				" FROM grupo AS G " . 
				" INNER JOIN usuario AS U ON U.usuario_id = G.usuario_id " . //Usuario que creó el grupo
				" INNER JOIN comision AS C ON (C.comision_id = G.comision_id AND C.activo = 'Y') " . 
				" INNER JOIN materia AS M ON (M.materia_id = C.materia_id AND M.activo = 'Y') " . 
				" INNER JOIN carrera AS R ON (R.carrera_id = M.carrera_id AND R.activo = 'Y') " . 
				" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
				" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
				" WHERE G.activo = 'Y' " .
				" AND C.comision_id = " . SafeSql($iIdComision);
$ResultGrupos = mysql_query($strSQLCommGrupos);


// Obtengo el listado de archivos.
$strSQLCommArchivos = " SELECT DISTINCT A.archivo_id, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_tipo, U.usuario_nombre, U.usuario_apellido " .
				" FROM archivo AS A " . 
				" INNER JOIN archivo_comision AS AC ON A.archivo_id = AC.archivo_id " .
				" INNER JOIN usuario_comision AS UC ON UC.comision_id = AC.comision_id " . 
				" INNER JOIN usuario AS U ON U.usuario_id = A.usuario_id " . //Usuario que creó el archivo
				" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
				" INNER JOIN materia AS M ON (M.materia_id = C.materia_id AND M.activo = 'Y') " . 
				" INNER JOIN carrera AS R ON (R.carrera_id = M.carrera_id AND R.activo = 'Y') " . 
				" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
				" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
				" WHERE UC.activo = 'Y' " .
				" AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . 
				" AND C.comision_id = " . SafeSql($iIdComision);
$ResultArchivos = mysql_query($strSQLCommArchivos);

include "header.php"; 
?>
<h3><?=$strComision?></h3>
<div class="SubMenu">
<a class="boton" href="mistps.php?idc=<?=encrypt($iIdComision)?>&return_url=<?=$strReturnUrl?>">Ver TPs</a>
<a class="boton" href="mismensajes.php?idc=<?=encrypt($iIdComision)?>&return_url=<?=$strReturnUrl?>">Ver Mensajes</a>		
</div>	
<table border="0" cellpadding="2" cellspacing="2" width="100%" align="left">
	<tr>
		<td align="left" style="font-weight:bold;">Código:</td>
		<td align="left"><?=$strComisionCodigo?></td>
	</tr>
	<tr>
		<td align="left" style="font-weight:bold;">Periodo:</td>
		<td align="left"><?=$strPeriodo?></td>
	</tr>
	<tr>
		<td align="left" style="font-weight:bold;">Modalidad:</td>
		<td align="left">
			<?=$strTipoUsuario?>
		</td>
	</tr>
	
</table>
<br />
<h3>Grupos</h3>
<div class="SubMenu">
<a class="boton" href="grupoform.php?idc=<?=encrypt($iIdComision)?>&action=new&return_url=<?=$strReturnUrl?>">Crear nuevo grupo</a>
</div>
<div class="TableBgBig">
	<table border="0" cellspacing="0" cellpadding="0">
	<thead class="HeaderStyle">
		<tr>
			<th>Creado</th>
			<th>Nombre</th>
			<th>Integrantes</th>
			<th>Acci&oacute;n</th>
		</tr>
	</thead>
	<?
	if(mysql_num_rows($ResultGrupos)){
		$ii = 0;
		while($oRs = mysql_fetch_array($ResultGrupos)){
			$strCreado = "por " . $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"];
			?>
			<tr class="<? if ($ii % 2  != 0){ ?>RowStyle<? }else{?>AltRowStyle<? }?>">
				<td class="TDleft"><?=$strCreado?></td>
				<td><?=$oRs["grupo_nombre"]?></td>
				<td>
					<?=ObtenerGrupoIntegrantes($oRs["grupo_id"])?><br />
				</td>
				<td class="TDright" align="center">
					<? $vecIds = explode(",", ObtenerGrupoIdIntegrantes($oRs["grupo_id"]));
					if ( in_array($_SESSION["UserId"], $vecIds) == true){?>
						<a href="grupoform.php?id=<?=encrypt($oRs["grupo_id"])?>&idc=<?=GetParameter("id")?>&action=edit&return_url=<?=$strReturnUrl?>">
						<img src="images/lapiz.png" title="Editar" alt="Editar" style="border:0; width: 32px;" /></a>
						<a href="javascript:ConfirmDelete('grupoform.php?btnBorrar=yes&id=<?=encrypt($oRs["grupo_id"])?>&return_url=<?=urlencode($strReturnUrl)?>', 'Desea darse de baja del grupo?');">
					<img src="images/desuscribirse.png" title="Darme de Baja" alt="Darme de Baja" style="border:0; width: 32px;" /></a>
					<?
					}else{?>
						No posee permisos en este grupo.
					<?
					}?>
				</td>
			</tr>
		<?
		}
	}else{?>
		<tr>
			<td colspan="3" class="TDleftright" width="600"><b>No hay grupos.</b></td>
		</tr>
	<?
	}
	/* Liberar conjunto de resultados */
	mysql_free_result($ResultGrupos);
	?>
	</table>	
</div>

<h3>Archivos</h3>
<div class="SubMenu">
<a class="boton" href="archivoform.php?idc=<?=encrypt($iIdComision)?>&action=new&return_url=<?=$strReturnUrl?>">Subir archivo</a>
</div>
<div class="TableBgBig">
	<table border="0" cellspacing="0" cellpadding="0">
	<thead class="HeaderStyle">
		<tr>
		<th>Creado</th>
		<th>Título</th>
		<th>Archivo</th>
		<th>Acci&oacute;n</th>
		</tr>
	</thead>
	<?
	if(mysql_num_rows($ResultArchivos)){
		$ii = 0;
		while($oRs = mysql_fetch_array($ResultArchivos)){
			$strCreado = DateTimeFormat($oRs["archivo_fechaalta"]) . " por " . $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"];
			?>
			<tr class="<? if ($ii % 2  != 0){ ?>RowStyle<? }else{?>AltRowStyle<? }?>">
				<td class="TDleft"><?=$strCreado?></td>
				<td><?=$oRs["archivo_titulo"]?></td>
				<td align="center"><a href="<?=$DIR_ARCHIVOS . $oRs["archivo_archivo"]?>" target="_blank"><img src="images/download.png" title="Descargar" alt="Descargar" style="border:0; width: 36px;" /></a></td>
				<td class="TDright" align="center"><a  href="archivodetalle.php?id=<?=encrypt($oRs["archivo_id"])?>&return_url=<?=$strReturnUrl?>"><img src="images/lupa.png" title="Ver Detalle" alt="Ver Detalle" style="border:0; width: 32px;" /></a></td>
			</tr>
		<?
		}
	}else{?>
		<tr>
			<td colspan="4" class="TDleftright" width="600"><b>No hay archivos.</b></td>
		</tr>
	<?
	}
	/* Liberar conjunto de resultados */
	mysql_free_result($ResultArchivos);
	?>
	</table>
</div>

<? 
include "common/inc_database_close.php";
include "footer.php";?>
