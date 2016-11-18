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

// Obtengo el listado de archivos.
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
				" WHERE 1 = 1 ";

if($iIdGrupo != ""){
	$strSQLCommArchivos = $strSQLCommArchivos . " AND CTOTAL.grupo_id = " . SafeSql($iIdGrupo);
}
if($iIdComision != ""){
	$strSQLCommArchivos = $strSQLCommArchivos . " AND CTOTAL.comision_id = " . SafeSql($iIdComision);
}
if($iIdMateria != ""){
	$strSQLCommArchivos = $strSQLCommArchivos . " AND CTOTAL.materia_id = " . SafeSql($iIdMateria);
}
if($iIdCarrera != ""){
	$strSQLCommArchivos = $strSQLCommArchivos . " AND CTOTAL.carrera_id = " . SafeSql($iIdCarrera);
}
if($iIdFacultad != ""){
	$strSQLCommArchivos = $strSQLCommArchivos . " AND CTOTAL.facultad_id = " . SafeSql($iIdFacultad);
}
if($iIdUniversidad != ""){
	$strSQLCommArchivos = $strSQLCommArchivos . " AND CTOTAL.universidad_id = " . SafeSql($iIdUniversidad);
}
if(GetParameter("fecha_1_1") != ""){
	$strFechaDesde = GetParameter('fecha_1_1') ."/". GetParameter('fecha_1_2') . "/". GetParameter('fecha_1_3');
	$strSQLCommArchivos = $strSQLCommArchivos . " AND CTOTAL.archivo_fechaalta >= " . SqlDateTime($strFechaDesde . " 00:00:00");
}
if(GetParameter("fecha_2_1") != ""){
	$strFechaHasta = GetParameter('fecha_2_1') ."/". GetParameter('fecha_2_2') . "/". GetParameter('fecha_2_3');
	$strSQLCommArchivos = $strSQLCommArchivos . " AND CTOTAL.archivo_fechaalta <= " . SqlDateTime($strFechaHasta . " 23:59:59");
}
if(GetParameter("txtTitulo") != ""){
	$strSQLCommArchivos = $strSQLCommArchivos . " AND (LCASE(CTOTAL.archivo_titulo) LIKE '%" . strtolower(SafeSql(GetParameter("txtTitulo"))) . "%'" .
						  " OR LCASE(CTOTAL.archivo_descripcion) LIKE '%" . strtolower(SafeSql(GetParameter("txtTitulo"))) . "%')";
}
if(GetParameter("txtNombre") != ""){
	$strSQLCommArchivos = $strSQLCommArchivos . " AND LCASE(CTOTAL.usuario_nombre) LIKE '%" . strtolower(SafeSql(GetParameter("txtNombre"))) . "%'";
}
if(GetParameter("txtApellido") != ""){
	$strSQLCommArchivos = $strSQLCommArchivos . " AND LCASE(CTOTAL.usuario_apellido) LIKE '%" . strtolower(SafeSql(GetParameter("txtApellido"))) . "%'";
}

$strSQLCommArchivos = $strSQLCommArchivos . " ORDER BY CTOTAL.archivo_fechaalta DESC ";
//echo($strSQLCommArchivos);exit();
// ******** PAGINADOR
$PagRowsPerPage = 20;
$strPaginador = PaginationImg($strSQLCommArchivos, $strParams);

if ( $PagAbsolutePosition >= 0 && $PagRowsPerPage >= 0 )
{
	$strSQLCommArchivos .= " LIMIT $PagAbsolutePosition, $PagRowsPerPage ";
}
// ******** FIN: PAGINADOR
$Result = mysql_query($strSQLCommArchivos);

//Seteo los default
if($iIdUniversidad == "")$iIdUniversidad = "0";
if($iIdFacultad == "") $iIdFacultad = "0";
if($iIdCarrera == "") $iIdCarrera= "0";
if($iIdMateria == "") $iIdMateria= "0";
if($iIdComision == "") $iIdComision= "0";
if($iIdGrupo == "") $iIdGrupo= "0";

include "header.php"; 
?>
<h2>Mis Archivos Recibidos</h2>
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
				<input class="MiniTxtFiltro" id="fecha_1_1" name="fecha_1_1" readonly="true" class="element text" size="1"  maxlength="2" value="<?=GetParameter("fecha_1_1")?>" type="text"><label for="fecha_1_1"></label>
				<input class="MiniTxtFiltro" id="fecha_1_2" name="fecha_1_2" readonly="true" class="element text" size="1" maxlength="2" value="<?=GetParameter("fecha_1_2")?>" type="text"><label for="fecha_1_2"></label>
				<input class="MiniTxtFiltro" id="fecha_1_3" name="fecha_1_3" readonly="true" class="element text" size="3"  maxlength="4" value="<?=GetParameter("fecha_1_3")?>" type="text" style="width: 40px;"><label for="fecha_1_3"></label>
				
				<span id="calendar_1" ><img id="cal_img_1" class="AcomodaCalendario" src="images/calendar.gif" alt="Elige una fecha." ></span>
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
					Fecha Hasta:
				</div>
				<input class="MiniTxtFiltro" id="fecha_2_1" name="fecha_2_1" readonly="true" class="element text" size="1"  maxlength="2" value="<?=GetParameter("fecha_2_1")?>" type="text"><label for="fecha_2_1"></label>
				<input class="MiniTxtFiltro" id="fecha_2_2" name="fecha_2_2" readonly="true" class="element text" size="1" maxlength="2" value="<?=GetParameter("fecha_2_2")?>" type="text"><label for="fecha_2_2"></label>
				<input class="MiniTxtFiltro" id="fecha_2_3" name="fecha_2_3" readonly="true" class="element text" size="3"  maxlength="4" value="<?=GetParameter("fecha_2_3")?>" type="text" style="width: 40px;"><label for="fecha_2_3"></label>
				
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
		</div>
	<div class="SeparaFiltro">
			<div class="SeparaModulo">
				<div class="LabelFiltro"> Palabra Clave: </div> <input type="text" name="txtTitulo" value="<?=GetParameter("txtTitulo")?>" class="TxtFiltro" />
			</div>
			<div class="SeparaModulo">
				<div class="LabelFiltro">Universidad: </div> 
				<? ObtenerComboUniversidad($_SESSION["UserId"], $iIdUniversidad, "onchange=\"TraerFacultades('" . $iIdFacultad . "','--Todas--','Y');\"")?>&nbsp;
			</div>
			<div class="SeparaModulo">
				<div class="LabelFiltro">Facultad: </div><? ObtenerComboFacultad($_SESSION["UserId"], $iIdFacultad, $iIdUniversidad, "onchange=\"TraerCarreras('" . $iIdCarrera . "','--Todas--','Y');\"")?>
			</div>
			<div class="SeparaModulo">
			<div class="LabelFiltro"> Carrera: </div><? ObtenerComboCarrera($_SESSION["UserId"], $iIdCarrera, $iIdFacultad, "onchange=\"TraerMaterias('" . $iIdMateria . "','--Todas--','Y');\"")?>
			</div>
		</div>
		<div class="SeparaFiltro">
		<div class="SeparaModulo">
			<div class="LabelFiltro">Materia: </div><? ObtenerComboMateria($_SESSION["UserId"], $iIdMateria, $iIdCarrera, "onchange=\"TraerComisiones('" . $iIdComision . "','--Todos--','Y');\"")?>
		</div>
		<div class="SeparaModulo">
			<div class="LabelFiltro">Comision: </div><? ObtenerComboComision($_SESSION["UserId"], $iIdComision, $iIdMateria, "")?>
		</div>
		<div class="SeparaModulo">
			<div class="LabelFiltro">
			Subido Por: </div><small class="LabelSmallFiltro">Nombre:</small> <input type="text" name="txtNombre" value="<?=GetParameter("txtNombre")?>" class="TxtFiltro"/>
		</div>
		<div class="SeparaModulo">
			<small class="LabelSmallFiltro">Apellido:</small> <input type="text" name="txtApellido" value="<?=GetParameter("txtApellido")?>" class="TxtFiltro" />
		</div>
	</div>
	<div class="botonesFiltro">
		<input class="botonFiltro" type="submit" name="btnBuscar" value="Buscar">
		<input class="botonFiltro" type="button" value="Cancelar" onclick="self.location='misarchivos.php';" name="btnVolver">
	</div>
</div>
</form>
<script language="javascript" type="text/javascript">
var frmvalidator2  = new Validator("frmForm");
frmvalidator2.EnableMsgsTogether();
//frmvalidator2.addValidation("cmbUniversidad","req","Debes ingresar una universidad.");
frmvalidator2.setAddnlValidationFunction(ChequeaFechasFiltroIncorrectas);

function Denunciar(sId){
		window.open('denunciar.php?t=A&id=' + sId,'denunciar','toolbar=no,menubar=no,resizable=no,scrollbars=yes,width=490,height=350, left=100, top=100');
}
</script>
</div>

	<div class="SubMenu">

	<a class="boton" href="misarchivoscreados.php">Mis Archivos Subidos</a>
	<a class="boton" href="archivoform.php?action=new&return_url=<?=$strReturnUrl?>">Nuevo Archivo</a>
</div>
<div class="TableBgBig">
	<table border="0" cellspacing="0" cellpadding="0">
	<thead class="HeaderStyle">
		<tr>
		<th>Subido</th>
		<th>Título</th>
		<th>Archivo</th>
		<th>Universidad</th>
		<th>Facultad</th>
		<th>Carrera</th>
		<th>Materia</th>
		<th>Comisión</th>
		<th>Grupo</th>
		<th>Acción</th>
		</tr>
	</thead>
	<?
	$bHayDatos = false;
	if(mysql_num_rows($Result)){
		$bHayDatos = true;
		$ii = 0;
		while($oRs = mysql_fetch_array($Result)){
		
			$strCreado = DateTimeFormat($oRs["archivo_fechaalta"]) . " por <strong>" . $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"] . "</strong>";
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
				<td><strong><? if($oRs["activo"] == "P"){?><span style="color:#FF0000; font-weight:bold;">El archivo ha sido denunciado.</span><? }else{?><?=$oRs["archivo_titulo"]?><? }?></strong></td>
				<td align="center"><? if($oRs["activo"] == "P"){?><span style="color:#FF0000; font-weight:bold;">El archivo ha sido denunciado.</span><? }else{?><a href="descarga.php?id=<?=encrypt($oRs["archivo_id"])?>&t=A"><img src="images/download.png" title="Descargar" style="width:40px;"/></a><? }?></td>
				<td><?=$strUniversidad?></td>
				<td><?=$strFacultad?></td>
				<td><?=$strCarrera?></td>
				<td><?=$strMateria?></td>
				<td><?=$strComision?></td>
				<td><?=$strGrupo?></td>
				<td class="TDright">
				<? if($oRs["activo"] != "P"){?>
				<a  href="archivover.php?id=<?=encrypt($oRs["archivo_id"])?>&action=rec&return_url=<?=$strReturnUrl?>"><img src="images/lupa.png" title="Ver Detalles" style="width:20px;"/></a>
				<a href="javascript://;" onclick="Denunciar('<?=encrypt($oRs["archivo_id"])?>');">Denunciar</a>
				<? }?>
				</td>
			</tr>
	<?	}
	}else{?>
		<tr>
			<td colspan="10" width="800" class="TDleftright"><b>No posee archivos.</b></td>
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
<script language="javascript" type="text/javascript">
	window.onload=function(){
		<? if($iIdUniversidad != ""){?>
			TraerFacultades('<?=$iIdFacultad?>', '--Todas--', 'Y');
		<? }
		 if($iIdFacultad != ""){?>
			TraerCarreras('<?=$iIdCarrera?>', '--Todas--', 'Y');
		<? }
		if($iIdCarrera != ""){?>
			TraerMaterias('<?=$iIdMateria?>', '--Todas--', 'Y');
		<? }
		 if($iIdMateria != ""){?>
			TraerComisiones('<?=$iIdComision?>', '--Todas--', 'Y');
		<? }
		if($iIdComision != ""){?>
			TraerGrupos('<?=$iIdGrupo?>', '--Todos--', 'Y');
		<? }?>
	}
</script>