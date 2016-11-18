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

$iIdMensaje = "0";
if(GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id")))){
	$iIdMensaje = decrypt(GetParameter("id"));
}

$iIdUniversidad = "";
if(GetParameter("idu") != "" && is_numeric(decrypt(GetParameter("idu")))){
	$iIdUniversidad = decrypt(GetParameter("idu"));
}elseif(GetParameter("cmbUniversidad") != "" && is_numeric(GetParameter("cmbUniversidad"))){
	$iIdUniversidad = GetParameter("cmbUniversidad");
}
$iIdFacultad = "";
if(GetParameter("idf") != "" && is_numeric(decrypt(GetParameter("idf")))){
	$iIdFacultad = decrypt(GetParameter("idf"));
}elseif(GetParameter("cmbFacultad") != "" && is_numeric(GetParameter("cmbFacultad"))){
	$iIdFacultad = GetParameter("cmbFacultad");
}
$iIdCarrera= "";
if(GetParameter("idr") != "" && is_numeric(decrypt(GetParameter("idr")))){
	$iIdCarrera = decrypt(GetParameter("idr"));
}elseif(GetParameter("cmbCarrera") != "" && is_numeric(GetParameter("cmbCarrera"))){
	$iIdCarrera = GetParameter("cmbCarrera");
}
$iIdMateria= "";
if(GetParameter("idm") != "" && is_numeric(decrypt(GetParameter("idm")))){
	$iIdMateria = decrypt(GetParameter("idm"));
}elseif(GetParameter("cmbMateria") != "" && is_numeric(GetParameter("cmbMateria"))){
	$iIdMateria = GetParameter("cmbMateria");
}
$iIdComision= "";
if(GetParameter("idc") != "" && is_numeric(decrypt(GetParameter("idc")))){
	$iIdComision = decrypt(GetParameter("idc"));
}elseif(GetParameter("cmbComision") != "" && is_numeric(GetParameter("cmbComision"))){
	$iIdComision = GetParameter("cmbComision");
}
$iIdGrupo= "";
if(GetParameter("idg") != "" && is_numeric(decrypt(GetParameter("idg")))){
	$iIdGrupo = decrypt(GetParameter("idg"));
}elseif(GetParameter("cmbGrupo") != "" && is_numeric(GetParameter("cmbGrupo"))){
	$iIdGrupo = GetParameter("cmbGrupo");
}
$iIdUsuario= "";
if(GetParameter("idp") != "" && is_numeric(decrypt(GetParameter("idp")))){
	$iIdUsuario = decrypt(GetParameter("idp"));
}elseif(GetParameter("cmbUsuario") != "" && is_numeric(GetParameter("cmbUsuario"))){
	$iIdUsuario = GetParameter("cmbUsuario");
}

$strParams = "fecha_1_1=" . GetParameter("fecha_1_1") . 
			 "&fecha_1_2=" . GetParameter("fecha_1_2") . 
			 "&fecha_1_2=" . GetParameter("fecha_1_2") . 
			 "&fecha_2_1=" . GetParameter("fecha_2_1") . 
			 "&fecha_2_2=" . GetParameter("fecha_2_2") . 
			 "&fecha_2_2=" . GetParameter("fecha_2_2") . 
			 "&cmbUniversidad=" . GetParameter("cmbUniversidad") . 
			 "&cmbFacultad=" . GetParameter("cmbFacultad") . 
			 "&cmbCarrera=" . GetParameter("cmbCarrera") . 
			 "&cmbMateria=" . GetParameter("cmbMateria") . 
			 "&cmbComision=" . GetParameter("cmbComision") . 
			 "&cmbGrupo=" . GetParameter("cmbGrupo") . 
			 "&txtNombre=" . GetParameter("txtNombre") . 
			 "&txtApellido=" . GetParameter("txtApellido") . 
			 "&txtTitulo=" . GetParameter("txtTitulo");

// ARMO RETURN URL
$strReturnUrl = $_SERVER["SCRIPT_NAME"];
if(isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] != ""){
	$strReturnUrl .=  "?" . $_SERVER["QUERY_STRING"];
}else {
	$strReturnUrl .=  "?" . $strParams;
}
$strReturnUrl = urlencode($strReturnUrl);

include "common/inc_database_open.php";

//Borro el mensaje creado por el usuario
if(GetParameter("btnBorrar") == "yes"){
	$strSQLComm = " UPDATE mensaje SET " .
				  " mensaje_activo = 'N' " . 
				  " WHERE mensaje_id = " . SafeSql($iIdMensaje) .
				  " AND usuario_id = " . SafeSql($_SESSION["UserId"]);
	mysql_query($strSQLComm);
}

// Obtengo el listado de mensajes.
$strSQLCommMensajes = " SELECT DISTINCT mensaje_id, mensaje_activo, mensaje_fecha, mensaje_titulo, mensaje_mensaje, mensaje_tipo, usuario_id, usuario_nombre, usuario_apellido, " . 
				" grupo, comision, materia, carrera, facultad, universidad, grupo_id, comision_id, materia_id, carrera_id, facultad_id, universidad_id, usuarioenviado_id, usuarioenviado_nombre, usuarioenviado_apellido " .
				" FROM " .  
				" ((SELECT M.mensaje_id, M.mensaje_activo, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
				" 'PRIVADO' AS grupo, 'PRIVADO' AS comision, 'PRIVADO' AS materia, 'PRIVADO' AS carrera, 'PRIVADO' AS facultad, 'PRIVADO' AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, 0 AS carrera_id, 0 AS facultad_id, 0 AS universidad_id, MU.usuario_id AS usuarioenviado_id, UMU.usuario_nombre AS usuarioenviado_nombre, UMU.usuario_apellido AS usuarioenviado_apellido " .
				" FROM mensaje AS M " . 
				" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
				" INNER JOIN mensaje_usuario AS MU ON (M.mensaje_id = MU.mensaje_id) " . //Mensajes Privados. 
				" INNER JOIN usuario AS UMU ON (UMU.usuario_id = MU.usuario_id) " . //Mensajes Privados. 
				" )UNION( " .
				" SELECT M.mensaje_id, M.mensaje_activo, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
				" G.grupo_nombre AS grupo, C.comision_titulo AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, G.grupo_id AS grupo_id, C.comision_id AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id, 0 AS usuarioenviado_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
				" FROM mensaje AS M " . 
				" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
				" INNER JOIN mensaje_grupo AS MG ON M.mensaje_id = MG.mensaje_id " . //Mensajes de Grupos 
				" INNER JOIN grupo AS G ON (G.grupo_id = MG.grupo_id AND G.activo = 'Y') " . 
				" INNER JOIN usuario_grupo AS UG ON (UG.grupo_id = G.grupo_id AND UG.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " . 
				" INNER JOIN comision AS C ON (C.comision_id = G.comision_id AND C.activo = 'Y') " . 
				" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
				" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
				" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
				" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
				" WHERE M.mensaje_activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )UNION( " .
				" SELECT M.mensaje_id, M.mensaje_activo, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
				" 'TODOS' AS grupo, C.comision_titulo AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, C.comision_id AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id, 0 AS usuarioenviado_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
				" FROM mensaje AS M " . 
				" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
				" INNER JOIN mensaje_comision AS MC ON M.mensaje_id = MC.mensaje_id " .  //Mensajes de Comisiones
				" INNER JOIN comision AS C ON (C.comision_id = MC.comision_id AND C.activo = 'Y') " . 
				" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
				" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
				" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
				" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
				" WHERE M.mensaje_activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )UNION( " .
				" SELECT M.mensaje_id, M.mensaje_activo, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
				" 'TODOS' AS grupo, 'TODAS' AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id, 0 AS usuarioenviado_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
				" FROM mensaje AS M " . 
				" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
				" INNER JOIN mensaje_materia AS MM ON M.mensaje_id = MM.mensaje_id " .  //Mensajes de Materias
				" INNER JOIN comision AS C ON (C.materia_id = MM.materia_id AND C.activo = 'Y') " . 
				" INNER JOIN materia AS MA ON (MA.materia_id = MM.materia_id AND MA.activo = 'Y') " . 
				" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
				" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
				" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
				" WHERE M.mensaje_activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )UNION( " .
				" SELECT M.mensaje_id, M.mensaje_activo, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
				" 'TODAS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id, 0 AS usuarioenviado_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
				" FROM mensaje AS M " . 
				" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
				" INNER JOIN mensaje_carrera AS MR ON M.mensaje_id = MR.mensaje_id " .  //Mensajes de Carreras
				" INNER JOIN materia AS MA ON (MA.carrera_id = MR.carrera_id AND MA.activo = 'Y') " . 
				" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
				" INNER JOIN carrera AS R ON (R.carrera_id = MR.carrera_id AND R.activo = 'Y') " . 
				" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
				" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
				" WHERE M.mensaje_activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )UNION( " .
				" SELECT M.mensaje_id, M.mensaje_activo, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
				" 'TODOS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, 'TODAS' AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, 0 AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id, 0 AS usuarioenviado_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
				" FROM mensaje AS M " . 
				" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
				" INNER JOIN mensaje_facultad AS MF ON M.mensaje_id = MF.mensaje_id " .  //Mensajes de Facultades
				" INNER JOIN carrera AS R ON (R.facultad_id = MF.facultad_id AND R.activo = 'Y') " . 
				" INNER JOIN materia AS MA ON (MA.carrera_id = R.carrera_id AND MA.activo = 'Y') " . 
				" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
				" INNER JOIN facultad AS F ON (F.facultad_id = MF.facultad_id AND F.activo = 'Y') " . 
				" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
				" WHERE M.mensaje_activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )UNION( " .
				" SELECT M.mensaje_id, M.mensaje_activo, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
				" 'TODOS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, 'TODAS' AS carrera, 'TODAS' AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, 0 AS carrera_id, 0 AS facultad_id, UN.universidad_id AS universidad_id, 0 AS usuarioenviado_id, 'TODOS' AS usuarioenviado_nombre, '' AS usuarioenviado_apellido " .
				" FROM mensaje AS M " . 
				" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
				" INNER JOIN mensaje_universidad AS MU ON M.mensaje_id = MU.mensaje_id " .  //Mensajes de Universidades
				" INNER JOIN facultad AS F ON (F.universidad_id = MU.universidad_id AND F.activo = 'Y') " . 
				" INNER JOIN carrera AS R ON (R.facultad_id = F.facultad_id AND R.activo = 'Y') " . 
				" INNER JOIN materia AS MA ON (MA.carrera_id = R.carrera_id AND MA.activo = 'Y') " . 
				" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
				" INNER JOIN universidad AS UN ON (UN.universidad_id = MU.universidad_id AND UN.activo = 'Y') " . 
				" WHERE M.mensaje_activo <> 'N' " .
				" AND U.activo = 'Y' " .
				" )) AS CTOTAL " .
				" WHERE CTOTAL.usuario_id = " . SafeSql($_SESSION["UserId"]);

if($iIdGrupo != ""){
	$strSQLCommMensajes = $strSQLCommMensajes . " AND CTOTAL.grupo_id = " . SafeSql($iIdGrupo);
}
if($iIdComision != ""){
	$strSQLCommMensajes = $strSQLCommMensajes . " AND CTOTAL.comision_id = " . SafeSql($iIdComision);
}
if($iIdMateria != ""){
	$strSQLCommMensajes = $strSQLCommMensajes . " AND CTOTAL.materia_id = " . SafeSql($iIdMateria);
}
if($iIdCarrera != ""){
	$strSQLCommMensajes = $strSQLCommMensajes . " AND CTOTAL.carrera_id = " . SafeSql($iIdCarrera);
}
if($iIdFacultad != ""){
	$strSQLCommMensajes = $strSQLCommMensajes . " AND CTOTAL.facultad_id = " . SafeSql($iIdFacultad);
}
if($iIdUniversidad != ""){
	$strSQLCommMensajes = $strSQLCommMensajes . " AND CTOTAL.universidad_id = " . SafeSql($iIdUniversidad);
}
if(GetParameter("fecha_1_1") != ""){
	$strFechaDesde = GetParameter('fecha_1_1') ."/". GetParameter('fecha_1_2') . "/". GetParameter('fecha_1_3');
	$strSQLCommMensajes = $strSQLCommMensajes . " AND CTOTAL.mensaje_fecha >= " . SqlDateTime($strFechaDesde . " 00:00:00");
}
if(GetParameter("fecha_2_1") != ""){
	$strFechaHasta = GetParameter('fecha_2_1') ."/". GetParameter('fecha_2_2') . "/". GetParameter('fecha_2_3');
	$strSQLCommMensajes = $strSQLCommMensajes . " AND CTOTAL.mensaje_fecha <= " . SqlDateTime($strFechaHasta . " 23:59:59");
}
if(GetParameter("txtTitulo") != ""){
	$strSQLCommMensajes = $strSQLCommMensajes . " AND (LCASE(CTOTAL.mensaje_titulo) LIKE '%" . strtolower(SafeSql(GetParameter("txtTitulo"))) . "%'" .
						  " OR LCASE(CTOTAL.mensaje_mensaje) LIKE '%" . strtolower(SafeSql(GetParameter("txtTitulo"))) . "%')";
}
if(GetParameter("txtNombre") != ""){
	$strSQLCommMensajes = $strSQLCommMensajes . " AND LCASE(CTOTAL.usuarioenviado_nombre) LIKE '%" . strtolower(SafeSql(GetParameter("txtNombre"))) . "%'";
}
if(GetParameter("txtApellido") != ""){
	$strSQLCommMensajes = $strSQLCommMensajes . " AND LCASE(CTOTAL.usuarioenviado_apellido) LIKE '%" . strtolower(SafeSql(GetParameter("txtApellido"))) . "%'";
}

$strSQLCommMensajes = $strSQLCommMensajes . " ORDER BY CTOTAL.mensaje_fecha DESC ";

// ******** PAGINADOR
$PagRowsPerPage = 20;
$strPaginador = PaginationImg($strSQLCommMensajes, $strParams);

if ( $PagAbsolutePosition >= 0 && $PagRowsPerPage >= 0 )
{
	$strSQLCommMensajes .= " LIMIT $PagAbsolutePosition, $PagRowsPerPage ";
}
// ******** FIN: PAGINADOR
$Result = mysql_query($strSQLCommMensajes);

//Seteo los default
if($iIdUniversidad == "")$iIdUniversidad = "0";
if($iIdFacultad == "") $iIdFacultad = "0";
if($iIdCarrera == "") $iIdCarrera= "0";
if($iIdMateria == "") $iIdMateria= "0";
if($iIdComision == "") $iIdComision= "0";
if($iIdGrupo == "") $iIdGrupo= "0";

include "header.php"; 
?>
<h2>Mis Mensajes (Mensajes enviados)</h2>

<div class="SpaceFiltroBusquedaBig">
<p class="NombreFiltro">Filtro de Búsqueda</p><hr class="HRFiltro" align="left"/>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm">
	<input type="hidden" name="btnBorrar" id="btnBorrar" />
	<input type="hidden" name="idc" id="idc" />
	
	<div class="Filtro">
		<div class="SeparaFiltro">
			<div class="SeparaModulo">
				<div class="LabelFiltro">
					Fecha Desde: 
				</div>
				<input class="MiniTxtFiltro" id="fecha_1_1" name="fecha_1_1" readonly="true" size="1"  maxlength="2" value="<?=GetParameter("fecha_1_1")?>" type="text"><label for="fecha_1_1"></label>
				<input class="MiniTxtFiltro" id="fecha_1_2" name="fecha_1_2" readonly="true" size="1" maxlength="2" value="<?=GetParameter("fecha_1_2")?>" type="text"><label for="fecha_1_2"></label>
				<input class="MiniTxtFiltro" id="fecha_1_3" name="fecha_1_3" readonly="true" size="3"  maxlength="4" value="<?=GetParameter("fecha_1_3")?>" type="text" style="width: 40px;"><label for="fecha_1_3"></label>
	
				<span id="calendar_1"><img id="cal_img_1" src="images/calendar.gif" alt="Elige una fecha." class="AcomodaCalendario"></span>
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
			<div class="SeparaModulo">
				<div class="LabelFiltro">
					&nbsp;&nbsp;Fecha Hasta: 
				</div>
				<input class="MiniTxtFiltro" id="fecha_2_1" name="fecha_2_1" readonly="true" size="1"  maxlength="2" value="<?=GetParameter("fecha_2_1")?>" type="text"><label for="fecha_2_1"></label></span>
				<input class="MiniTxtFiltro" id="fecha_2_2" name="fecha_2_2" readonly="true"  size="1" maxlength="2" value="<?=GetParameter("fecha_2_2")?>" type="text"><label for="fecha_2_2"></label></span>
				<input class="MiniTxtFiltro" id="fecha_2_3" name="fecha_2_3" readonly="true" size="3"  maxlength="4" value="<?=GetParameter("fecha_2_3")?>" type="text" style="width: 40px;"><label for="fecha_2_3"></label></span>
		
				<span id="calendar_2"><img id="cal_img_2" src="images/calendar.gif" alt="Elige una fecha." class="AcomodaCalendario"></span>
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
			<div class="SeparaModulo">
				<div class="LabelFiltro">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Palabra Clave:&nbsp;
				</div>
				<input type="text" name="txtTitulo" value="<?=GetParameter("txtTitulo")?>" class="TxtFiltro" />
			</div>
		</div>
		<div class="SeparaFiltro">
			<div class="SeparaModulo">
				<div class="LabelFiltro"> Universidad: </div> 
				<? ObtenerComboUniversidad($_SESSION["UserId"], $iIdUniversidad, "onchange=\"TraerFacultades('" . $iIdFacultad . "','--Todas--','Y');\"")?>&nbsp;
			</div>
			<div class="SeparaModulo">
				<div class="LabelFiltro"> Facultad: </div> <? ObtenerComboFacultad($_SESSION["UserId"], $iIdFacultad, $iIdUniversidad, "onchange=\"TraerCarreras('" . $iIdCarrera . "','--Todas--','Y');\"")?>
			</div>
			<div class="SeparaModulo">
			<div class="LabelFiltro"> &nbsp;Carrera:  </div> <? ObtenerComboCarrera($_SESSION["UserId"], $iIdCarrera, $iIdFacultad, "onchange=\"TraerMaterias('" . $iIdMateria . "','--Todas--','Y');\"")?>
			</div>
			<div class="SeparaModulo">
				<div class="LabelFiltro"> &nbsp;Materia: </div> <? ObtenerComboMateria($_SESSION["UserId"], $iIdMateria, $iIdCarrera, "onchange=\"TraerComisiones('" . $iIdComision . "','--Todos--','Y');\"")?>
			</div>
		</div>
		<div class="SeparaFiltro">
			<div class="SeparaModulo">
			<div class="LabelFiltro">Comision: </div> <? ObtenerComboComision($_SESSION["UserId"], $iIdComision, $iIdMateria, "onchange=\"TraerGrupos('" . $iIdGrupo . "','--Todos--','Y');\"")?>
			</div><div class="SeparaModulo">
			<div class="LabelFiltro">Grupo: </div> <? ObtenerComboGrupo($_SESSION["UserId"], $iIdGrupo, $iIdComision, "")?>
			</div><div class="SeparaModulo">
			<div class="LabelFiltro">&nbsp;&nbsp;Enviado Por: </div>  <small class="LabelSmallFiltro">Nombre:</small> <input type="text" name="txtNombre" value="<?=GetParameter("txtNombre")?>" class="TxtFiltro" />
			</div><div class="SeparaModulo">
			<small class="LabelSmallFiltro">Apellido:</small> <input type="text" name="txtApellido" value="<?=GetParameter("txtApellido")?>" class="TxtFiltro" />
			</div>
		</div>
	</div>
	<div class="botonesFiltro">
		<input class="botonFiltro" type="submit" name="btnBuscar" value="Buscar">
		<input class="botonFiltro" type="button" value="Cancelar" onclick="self.location='mismensajesenviados.php';" name="btnVolver">
	</div>
</form>
 <script language="javascript" type="text/javascript">
var frmvalidator2  = new Validator("frmForm");
frmvalidator2.EnableMsgsTogether();
//frmvalidator2.addValidation("cmbUniversidad","req","Debes ingresar una universidad.");
frmvalidator2.setAddnlValidationFunction(ChequeaFechasFiltroIncorrectas);
</script>

</div>
	<div class="SubMenu">
<a class="boton" href="mismensajes.php">Bandeja de Entrada</a>
<a class="boton" href="mensajeform.php?action=new&return_url=<?=$strReturnUrl?>">Nuevo Mensaje</a>
</div>
<div class="TableBgBig">
	<table border="0" cellspacing="0" cellpadding="0">
	<thead class="HeaderStyle">
		<tr>
		<th>Creado</th>
		<th>Título</th>
		<th>Universidad</th>
		<th>Facultad</th>
		<th>Carrera</th>
		<th>Materia</th>
		<th>Comisión</th>
		<th>Grupo</th>
		<th>Usuario</th>
		<th width="116">Acción</th>
		</tr>
	</thead>
	<?
	$bHayDatos = false;
	if(mysql_num_rows($Result)){
		$bHayDatos = true;
		$ii = 0;
		while($oRs = mysql_fetch_array($Result)){
		
			$strCreado = DateTimeFormat($oRs["mensaje_fecha"]) . " por <strong>" . $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"] . "</strong>";
			$strGrupo = $oRs["grupo"];
			$strComision = $oRs["comision"];
			$strMateria = $oRs["materia"];
			$strCarrera = $oRs["carrera"];
			$strFacultad = $oRs["facultad"];
			$strUniversidad = $oRs["universidad"];
			$ii++;
			?>
			<tr class="<? if ($ii % 2  != 0){ ?>RowStyle<? }else{?>AltRowStyle<? }?>">
				<td class="TDleft"><?=$strCreado?></td>
				<td><strong><? if($oRs["mensaje_activo"] == "P"){?><span style="color:#FF0000;">El mensaje ha sido denunciado.</span><? }else{?><?=$oRs["mensaje_titulo"]?><? }?></strong></td>
				<td><?=$strUniversidad?></td>
				<td><?=$strFacultad?></td>
				<td><?=$strCarrera?></td>
				<td><?=$strMateria?></td>
				<td><?=$strComision?></td>
				<td><?=$strGrupo?></td>
				<td><?=$oRs["usuarioenviado_nombre"] . " " . $oRs["usuarioenviado_apellido"]?></td>
				<td class="TDright">
				<? if($oRs["mensaje_activo"] != "P"){?>
					<a href="mensajever.php?id=<?=encrypt($oRs["mensaje_id"])?>&action=env&return_url=<?=$strReturnUrl?>">
					<img src="images/ver_mensaje.png" title="Ver Mensaje" alt="Ver Mensaje" style="width: 35px; border:0;"/></a>
					<a  href="mensajeform.php?id=<?=encrypt($oRs["mensaje_id"])?>&action=edit&return_url=<?=$strReturnUrl?>">
					<img src="images/mail_forward.png" title="Reenviar Mensaje" alt="Reenviar Mensaje" style="width: 35px; border:0;"/>
					</a>
					<a href="javascript:ConfirmDelete('mismensajesenviados.php?btnBorrar=yes&id=<?=encrypt($oRs["mensaje_id"])?>&return_url=<?=urlencode($strReturnUrl)?>', 'Desea borrar el mensaje?');">
					<img src="images/trash_empty.png" title="Borrar Mensaje" alt="Borrar Mensaje" style="width: 35px; border:0;"/></a>
				<? }?>
				</td>
			</tr>
	<?	}
	}else{?>
		<tr>
			<td colspan="10" width="800" class="TDleftright"><b>No posee mensajes.</b></td>
		<tr>
	<?
	}
	/* Liberar conjunto de resultados */
	mysql_free_result($Result);?>
</table>
</div>
<br />
<div id="paginador" align="center">  
	<? if($bHayDatos){?>
		<?=$strPaginador?>
	<? }?>
</div>
<? 
include "common/inc_database_close.php";
include "footer.php"; ?>
