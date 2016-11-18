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

//Obtiene todas las notas de los TPs del alumno
function ObtenerNotas($iIdusuario){
	$strResultado = "<table border='0' cellspacing='0'><tr><td style='border:0px none black;border-bottom:1px solid black;' valign='top'>";
	$iIdTP = "";
	// Obtengo el listado de notas.
	$strSQLCommEntregas = " SELECT DISTINCT TP.tp_id, TP.tp_titulo, TP.tp_fechaentrega, E.entrega_version, E.entrega_fechaentrega, E.entrega_estado, N.nota_nota " . 
							" FROM trabajo_practico AS TP " . 
							" INNER JOIN usuario_comision AS UC2 ON (UC2.comision_id = TP.comision_id AND UC2.activo = 'Y' AND UC2.usuario_id = " . SafeSql($iIdusuario) . ") " .
							" LEFT JOIN entrega AS E ON (E.tp_id = TP.tp_id AND E.activo = 'Y') " . 
							" LEFT JOIN usuario_grupo AS UG ON (UG.grupo_id = E.grupo_id AND UG.usuario_id = " . SafeSql($iIdusuario) . ") " .
							" LEFT JOIN grupo AS G ON (G.grupo_id = UG.grupo_id AND G.activo = 'Y') " . 
							" LEFT JOIN usuario_comision AS UC ON (UC.comision_id = G.comision_id AND UC.usuario_id = " . SafeSql($iIdusuario) . ") " .
							" LEFT JOIN nota AS N ON N.entrega_id = E.entrega_id " . 
							" WHERE TP.activo = 'Y' " . 
							" ORDER BY TP.tp_fechaentrega DESC, tp_id DESC, E.entrega_fechaentrega DESC ";
	$ResultEntregas = mysql_query($strSQLCommEntregas);
	if(mysql_num_rows($ResultEntregas)){
		$iii = 0;
		while($oRs = mysql_fetch_array($ResultEntregas)){
			if($iIdTP != trim("".$oRs["tp_id"])){
				if($iii != 0){
					$strResultado = $strResultado . "</td></tr><tr><td style='border:0px none black;border-bottom:1px solid black;' valign='top'>";
				}
				$strResultado = $strResultado . $oRs["tp_titulo"] . " <strong>" . DateFormat($oRs["tp_fechaentrega"]) . " </strong></td><td style='border:0px none black;border-bottom:1px solid black;'>"; 
			}
			if($oRs["entrega_version"] != ""){
				$strResultado = $strResultado . " Entrega Nº " . $oRs["entrega_version"] . " <strong>" . DateTimeFormat($oRs["entrega_fechaentrega"]) . "</strong>";
				if($oRs["nota_nota"] != ""){
					$strResultado = $strResultado . " - <strong>" . $oRs["nota_nota"] . "</strong> <br>";
				}else{
					$strResultado = $strResultado . " - No Corregido <br>";
				}	
			}else{
				$strResultado = $strResultado . " No Entregado <br>";
			}
			$iii ++;
			$iIdTP = $oRs["tp_id"];
		}
	}
	mysql_free_result($ResultEntregas);
	
	$strResultado = $strResultado . "</td></tr></table>";
	
	return $strResultado;
}


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
$strTipo = "";

if(mysql_num_rows($Result)){
	if($oRs = mysql_fetch_array($Result)){
		$strPeriodo = DateFormat($oRs["comision_fechadesde"]) . " - " . DateFormat($oRs["comision_fechahasta"]);
		$strComision = $oRs["comision"];
		$strComisionCodigo = $oRs["comision_codigo"];
		$strMateria = $oRs["materia"];
		$strCarrera = $oRs["carrera"];
		$strFacultad = $oRs["facultad"];
		$strUniversidad = $oRs["universidad"];
		$strTipo = $oRs["usuario_tipo"];
		switch($strTipo){
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

//Cambio el estado del usuario
if($strTipo == "P" || $strTipo == "Y"){
	if(GetParameter("e") != ""){
		if(GetParameter("idu") != "" && is_numeric(decrypt(GetParameter("idu")))){
			$strSQLComm = " UPDATE usuario_comision SET " .
						  " activo = '" . SafeSql(GetParameter("e")) . "' " . 
						  " WHERE comision_id = " . SafeSql($iIdComision) .
						  " AND usuario_id = " . SafeSql(decrypt(GetParameter("idu")));
			mysql_query($strSQLComm);
			
			if(mysql_error() == ""){
				$strAccionMensaje = "BAJA";
				if(GetParameter("e") == "Y"){
					$strAccionMensaje = "ALTA";
				}
				$strNombreMensaje = "VirtualU";
				$strMail = "";
				$strTituloMensaje = $strAccionMensaje . " a comisión";
				$strMensaje = "Ha sido dado de " . $strAccionMensaje . " a la comisión: " . $strComision;
				$strMail = ObtenerMails(decrypt(GetParameter("idu")), "P", "'P','Y','L'");
				include "enviarmail.php";
				?><script language="javascript" type="text/javascript">EnviarMail('<?=$strNombreMensaje?>', '<?=$strMail?>', '<?=$strTituloMensaje?>', '<?=$strMensaje?>' );</script><?
			}
		}
	}
}

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

/*$strSQLCommArchivos = " SELECT DISTINCT A.archivo_id, A.activo, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_tipo, U.usuario_nombre, U.usuario_apellido " .
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
				" AND C.comision_id = " . SafeSql($iIdComision);*/
$strSQLCommArchivos = " SELECT DISTINCT archivo_id, activo, archivo_fechaalta, archivo_titulo, archivo_archivo, archivo_descripcion, archivo_tipo, usuario_id, usuario_nombre, usuario_apellido, " . 
				" grupo, comision, materia, carrera, facultad, universidad, grupo_id, comision_id, materia_id, carrera_id, facultad_id, universidad_id " .
				" FROM " .  
				" ((SELECT A.archivo_id, A.activo, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
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
				" WHERE A.activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )UNION( " .
				" SELECT A.archivo_id, A.activo, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
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
				" WHERE A.activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )UNION( " .
				" SELECT A.archivo_id, A.activo, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
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
				" WHERE A.activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )UNION( " .
				" SELECT A.archivo_id, A.activo, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
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
				" WHERE A.activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )UNION( " .
				" SELECT A.archivo_id, A.activo, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
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
				" WHERE A.activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )UNION( " .
				" SELECT A.archivo_id, A.activo, A.archivo_fechaalta, A.archivo_titulo, A.archivo_archivo, A.archivo_descripcion, A.archivo_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
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
				" WHERE A.activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )) AS CTOTAL " .
				" WHERE 1 = 1 " .
				" AND CTOTAL.comision_id = " . SafeSql($iIdComision) .
				" ORDER BY CTOTAL.archivo_fechaalta DESC ";
$ResultArchivos = mysql_query($strSQLCommArchivos);

// Obtengo los profesores.
$strSQLCommProfesores = " SELECT U.usuario_id, U.usuario_nombre, U.usuario_apellido, U.usuario_dni, T.tipodni_titulo, UC.usuario_tipo, UC.activo " .
				" FROM usuario_comision AS UC " . 
				" INNER JOIN usuario AS U ON (U.usuario_id = UC.usuario_id AND UC.activo <> 'N' AND UC.comision_id = " . SafeSql($iIdComision) . ")" .  
				" INNER JOIN tipodni AS T ON T.tipodni_id = U.tipodni_id " .  
				" INNER JOIN usuario_comision AS UC2 ON (UC.comision_id = UC2.comision_id AND UC2.activo = 'Y' AND UC2.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
				" WHERE U.activo = 'Y' " .
				" AND (UC.usuario_tipo = 'Y' OR UC.usuario_tipo = 'P') " .
				" ORDER BY U.usuario_apellido, U.usuario_nombre, U.usuario_dni";
$ResultProfesores = mysql_query($strSQLCommProfesores);

// Obtengo los alumnos.
$strSQLCommAlumnos = " SELECT U.usuario_id, U.usuario_nombre, U.usuario_apellido, U.usuario_dni, T.tipodni_titulo, UC.activo, UC2.usuario_tipo " .
				" FROM usuario_comision AS UC " . 
				" INNER JOIN usuario AS U ON (U.usuario_id = UC.usuario_id AND UC.activo <> 'N' AND UC.comision_id = " . SafeSql($iIdComision) . ")" .  
				" INNER JOIN tipodni AS T ON T.tipodni_id = U.tipodni_id " .  
				" INNER JOIN usuario_comision AS UC2 ON (UC.comision_id = UC2.comision_id AND UC2.activo = 'Y' AND UC2.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
				" WHERE U.activo = 'Y' " .
				" AND UC.usuario_tipo = 'L' " .
				" ORDER BY U.usuario_apellido, U.usuario_nombre, U.usuario_dni";
$ResultAlumnos = mysql_query($strSQLCommAlumnos);

$strCol = 3;

include "header.php"; 
?>
<script language="javascript" type="text/javascript">
	function Baja(id){
		if(confirm('Desea realmente dar de baja al usuario?')){
			self.location = 'comisiondetalle.php?id=<?=GetParameter("id")?>&idu=' + id + '&e=N&return_url=<?=urlencode(GetParameter("return_url"))?>';
		}
	}
</script>
<h3><?=$strComision?></h3>
<div class="SubMenu">
<a class="boton" href="mistps.php?idc=<?=encrypt($iIdComision)?>&return_url=<?=$strReturnUrl?>">Ver TPs</a>
<a class="boton" href="mismensajes.php?idc=<?=encrypt($iIdComision)?>&return_url=<?=$strReturnUrl?>">Ver Mensajes</a>		
</div>	
<div class="TableBgBig">
<table border="0" cellpadding="2" cellspacing="2" width="100%" align="left" style="font-size:12px;">
	<tr>
		<td align="right" style="font-weight:bold;">Código:</td>
		<td align="left"><?=$strComisionCodigo?></td>
	</tr>
	<tr>
		<td align="right" style="font-weight:bold;">Periodo:</td>
		<td align="left"><?=$strPeriodo?></td>
	</tr>
	<tr>
		<td align="right" style="font-weight:bold;">Modalidad:</td>
		<td align="left">
			<?=$strTipoUsuario?>
		</td>
	</tr>
</table>
</div>
<br />

<h3>Lista de Profesores</h3>
<div class="TableBgBig">
	<table border="0" cellspacing="0" cellpadding="0">
	<thead class="HeaderStyle">
		<tr>
			<th>Apellido, Nombre</th>
			<th>Modalidad</th>
			<th>Estado</th>
			<? if($strTipo == "P" || $strTipo == "Y"){$strCol = 4;?><th>Acci&oacute;n</th><? }?>
		</tr>
	</thead>
	<?
	if(mysql_num_rows($ResultProfesores)){
		$ii = 0;
		while($oRs = mysql_fetch_array($ResultProfesores)){
			$strNombre = $oRs["usuario_apellido"] . ", " . $oRs["usuario_nombre"];
			$strModalidad = "Profesor";
			if($oRs["usuario_tipo"] == "Y"){
				$strModalidad = "Ayudante / Profesor Adjunto";
			}
			$strEstado = "Activo";
			$strAccion = "";
			if($oRs["activo"] == "P"){
				$strEstado = "Pendiente de aprobaci&oacute;n";
				$strAccion = "Y";
			}
			?>
			<tr class="<? if ($ii % 2  != 0){ ?>RowStyle<? }else{?>AltRowStyle<? }?>">
				<td class="TDleft" valign="top"><?=$strNombre?></td>
				<td valign="top"><?=$strModalidad?></td>
				<td valign="top"><?=$strEstado?></td>
				<? if($strTipo == "P" || $strTipo == "Y"){?>
					<td valign="top">
						<? if($oRs["usuario_id"] != $_SESSION["UserId"] && $oRs["usuario_tipo"] != "P"){?>
							<? if($strAccion == "Y"){?>
								<a href="javascript://;" onclick="self.location='comisiondetalle.php?id=<?=GetParameter("id")?>&idu=<?=encrypt($oRs["usuario_id"])?>&e=Y&return_url=<?=urlencode(GetParameter("return_url"))?>';"><img src="images/button-ok.png" title="Aprobar Solicitud" alt="Aprobar Solicitud" style="width: 30px; border:0;"/></a>
							<? }?>
							<a href="javascript://;" onclick="Baja('<?=encrypt($oRs["usuario_id"])?>');"><img src="images/desuscribirse.png" title="Dar de Baja" alt="Dar de Baja" style="width: 30px; border:0;"/></a>
						<?
						}?>
					</td>
				<? }?>
			</tr>
		<?
		}
	}else{?>
		<tr>
			<td colspan="<?=$strCol?>" class="TDleftright" style="font-size:12px;"><b>No hay profesores inscriptos.</b></td>
		</tr>
	<?
	}
	/* Liberar conjunto de resultados */
	mysql_free_result($ResultProfesores);
	$strCol = 4;
	?>
	</table>	
</div>

<h3>Lista de Alumnos</h3>
<div class="TableBgBig">
	<table border="0" cellspacing="0" cellpadding="0">
	<thead class="HeaderStyle">
		<tr>
			<th>Apellido, Nombre</th>
			<th>DNI</th>
			<? if($strTipo == "Y" || $strTipo == "P"){$strCol = 5; ?><th>Notas</th><? }?>
			<th>Estado</th>
			<? if($strTipo == "Y" || $strTipo == "P"){$strCol = 5; ?><th>Acci&oacute;n</th><? }?>
		</tr>
	</thead>
	<?
	if(mysql_num_rows($ResultAlumnos)){
		$ii = 0;
		while($oRs = mysql_fetch_array($ResultAlumnos)){
			$strNombre = $oRs["usuario_apellido"] . ", " . $oRs["usuario_nombre"];
			$strDNI = $oRs["tipodni_titulo"] . ": " . $oRs["usuario_dni"];
			$strEstado = "Activo";
			$strAccion = "";
			if($oRs["activo"] == "P"){
				$strEstado = "Pendiente de aprobaci&oacute;n";
				$strAccion = "Y";
			}
			?>
			<tr class="<? if ($ii % 2  != 0){ ?>RowStyle<? }else{?>AltRowStyle<? }?>">
				<td class="TDleft" valign="top"><?=$strNombre?></td>
				<td valign="top"><?=$strDNI?></td>
				<? if($strTipo == "Y" || $strTipo == "P"){?><td><?=ObtenerNotas($oRs["usuario_id"])?></td><? }?>
				<td valign="top"><?=$strEstado?></td>
				<? if($strTipo == "P" || $strTipo == "Y"){?>
					<td valign="top">
						<? if($strAccion == "Y"){?>
							<a href="javascript://;" onclick="self.location='comisiondetalle.php?id=<?=GetParameter("id")?>&idu=<?=encrypt($oRs["usuario_id"])?>&e=Y&return_url=<?=urlencode(GetParameter("return_url"))?>';"><img src="images/button-ok.png" title="Aprobar Solicitud" alt="Aprobar Solicitud" style="width: 27px; border:0;"/></a>
						<? }?>
						<a href="javascript://;" onclick="Baja('<?=encrypt($oRs["usuario_id"])?>');"><img src="images/desuscribirse.png" title="Dar de Baja" alt="Dar de Baja" style="width: 30px; border:0;"/></a>
					</td>
				<?
				}?>
			</tr>
		<?
		}
	}else{?>
		<tr>
			<td colspan="<?=$strCol?>" class="TDleftright" style="font-size:12px;"><b>No hay alumnos inscriptos.</b></td>
		</tr>
	<?
	}
	/* Liberar conjunto de resultados */
	mysql_free_result($ResultAlumnos);
	?>
	</table>	
</div>
<hr />
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
			<td colspan="4" class="TDleftright" width="600"><b>No hay grupos.</b></td>
		</tr>
	<?
	}
	/* Liberar conjunto de resultados */
	mysql_free_result($ResultGrupos);
	?>
	</table>	
</div>
<script language="javascript" type="text/javascript">
function Denunciar(sId){
		window.open('denunciar.php?t=A&id=' + sId,'denunciar','toolbar=no,menubar=no,resizable=no,scrollbars=yes,width=490,height=350, left=100, top=100');
}
</script>
<h3>Archivos</h3>
<div class="SubMenu">
<a class="boton" href="archivoformcomision.php?idc=<?=encrypt($iIdComision)?>&action=new&return_url=<?=$strReturnUrl?>">Subir archivo</a>
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
				<td><strong><? if($oRs["activo"] == "P"){?><span style="color:#FF0000; font-weight:bold;">El archivo ha sido denunciado.</span><? }else{?><?=$oRs["archivo_titulo"]?><? }?></strong></td>
				<td align="center"><? if($oRs["activo"] == "P"){?><span style="color:#FF0000; font-weight:bold;">El archivo ha sido denunciado.</span><? }else{?><a href="descarga.php?id=<?=encrypt($oRs["archivo_id"])?>&t=A"><img src="images/download.png" title="Descargar" style="width:40px;"/></a><? }?></td>
				<td class="TDright" align="center">
				<? if($oRs["activo"] != "P"){?>
				<a  href="archivover.php?id=<?=encrypt($oRs["archivo_id"])?>&return_url=<?=$strReturnUrl?>"><img src="images/lupa.png" title="Ver Detalle" alt="Ver Detalle" style="border:0; width: 32px;" /></a>
				<a href="javascript://;" onclick="Denunciar('<?=encrypt($oRs["archivo_id"])?>');">Denunciar</a>
				<? }?>
				</td>
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
