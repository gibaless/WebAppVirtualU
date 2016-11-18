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
			 "&txtNombre=" . GetParameter("txtNombre") . 
			 "&txtApellido=" . GetParameter("txtApellido") . 
			 "&txtTitulo=" . GetParameter("txtTitulo") .
			 "&rdRango=" . GetParameter("rdRango");


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
$strSQLCommTPs = " SELECT DISTINCT TP.tp_id, TP.tp_titulo, TP.tp_fechaalta, TP.tp_fechaentrega, TP.tp_archivo, UR.usuario_id, UR.usuario_nombre, UR.usuario_apellido, C.comision_fechadesde, C.comision_fechahasta, U.tipo_usuario, UC.usuario_tipo, " . 
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
				" AND U.usuario_id = " . SafeSql($_SESSION["UserId"]);
				
if($iIdComision != ""){
	$strSQLCommTPs = $strSQLCommTPs . " AND C.comision_id = " . SafeSql($iIdComision);
}
if($iIdMateria != ""){
	$strSQLCommTPs = $strSQLCommTPs . " AND M.materia_id = " . SafeSql($iIdMateria);
}
if($iIdCarrera != ""){
	$strSQLCommTPs = $strSQLCommTPs . " AND R.carrera_id = " . SafeSql($iIdCarrera);
}
if($iIdFacultad != ""){
	$strSQLCommTPs = $strSQLCommTPs . " AND F.facultad_id = " . SafeSql($iIdFacultad);
}
if($iIdUniversidad != ""){
	$strSQLCommTPs = $strSQLCommTPs . " AND UN.universidad_id = " . SafeSql($iIdUniversidad);
}
if(GetParameter("fecha_1_1") != ""){
	$strFechaDesde = GetParameter('fecha_1_1') ."/". GetParameter('fecha_1_2') . "/". GetParameter('fecha_1_3');
	$strSQLCommTPs = $strSQLCommTPs . " AND TP.tp_fechaentrega >= " . SqlDateTime($strFechaDesde . " 00:00:00");
}
if(GetParameter("fecha_2_1") != ""){
	$strFechaHasta = GetParameter('fecha_2_1') ."/". GetParameter('fecha_2_2') . "/". GetParameter('fecha_2_3');
	$strSQLCommTPs = $strSQLCommTPs . " AND TP.tp_fechaentrega <= " . SqlDateTime($strFechaHasta . " 23:59:59");
}
if(GetParameter("txtTitulo") != ""){
	$strSQLCommTPs = $strSQLCommTPs . " AND (LCASE(TP.tp_titulo) LIKE '%" . strtolower(SafeSql(GetParameter("txtTitulo"))) . "%'" .
						  " OR LCASE(TP.tp_descripcion) LIKE '%" . strtolower(SafeSql(GetParameter("txtTitulo"))) . "%')";
}
if(GetParameter("txtNombre") != ""){
	$strSQLCommTPs = $strSQLCommTPs . " AND LCASE(UR.usuario_nombre) LIKE '%" . strtolower(SafeSql(GetParameter("txtNombre"))) . "%'";
}
if(GetParameter("txtApellido") != ""){
	$strSQLCommTPs = $strSQLCommTPs . " AND LCASE(UR.usuario_apellido) LIKE '%" . strtolower(SafeSql(GetParameter("txtApellido"))) . "%'";
}
if(GetParameter("rdRango") == "Rango"){
	$strRango = "Rango";
}else{
	$strSQLCommTPs = $strSQLCommTPs . " AND C.comision_fechadesde <= CURDATE() AND C.comision_fechahasta >= CURDATE() ";
	$strRango = "Actual";
}

$strSQLCommTPs = $strSQLCommTPs . " ORDER BY TP.tp_fechaalta DESC ";

// ******** PAGINADOR

//Seteo los default
if($iIdUniversidad == "")$iIdUniversidad = "0";
if($iIdFacultad == "") $iIdFacultad = "0";
if($iIdCarrera == "") $iIdCarrera= "0";
if($iIdMateria == "") $iIdMateria= "0";
if($iIdComision == "") $iIdComision= "0";

$PagRowsPerPage = 20;
$strPaginador = PaginationImg($strSQLCommTPs, $strParams);

if ( $PagAbsolutePosition >= 0 && $PagRowsPerPage >= 0 )
{
	$strSQLCommTPs .= " LIMIT $PagAbsolutePosition, $PagRowsPerPage ";
}
// ******** FIN: PAGINADOR
$Result = mysql_query($strSQLCommTPs);

include "header.php"; 
?>
<h2>Mis TPs</h2>
<div class="SpaceFiltroBusquedaBig">
<p class="NombreFiltro">Filtro de Búsqueda</p><hr class="HRFiltro" align="left"/>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm">
	<input type="hidden" name="btnBorrar" id="btnBorrar" />
	<input type="hidden" name="idc" id="idc" />
	
	<div class="Filtro">
		<div class="SeparaFiltro">
			<div class="SeparaModulo">
				<input type="radio" name="rdRango" value="Actual"<? if($strRango == "Actual"){?> checked="checked"<? }?>/> <span class="LabelSmallFiltro"><b>TPs de Comisiones Actuales</b></span>
			</div>
			<div class="SeparaModulo">
			<input type="radio" name="rdRango" value="Rango"<? if($strRango == "Rango"){?> checked="checked"<? }?>/> <span class="LabelSmallFiltro"><b>Todos los TPs</b></span>
			</div>
		</div>
		<div class="SeparaFiltro">
			<div class="SeparaModulo">
				<div class="LabelFiltro">
					Fecha de Entrega:
				</div>
				<small class="LabelSmallFiltro">Desde:</small> 
				<span><input id="fecha_1_1" name="fecha_1_1" readonly="true"   size="1"  maxlength="2" class="MiniTxtFiltro" value="<?=GetParameter("fecha_1_1")?>" type="text"><label for="fecha_1_1"></label></span>
				<span><input id="fecha_1_2" name="fecha_1_2" readonly="true" class="MiniTxtFiltro"  size="1" maxlength="2" value="<?=GetParameter("fecha_1_2")?>" type="text"><label for="fecha_1_2"></label></span>
				<span><input id="fecha_1_3" name="fecha_1_3" style="width: 38px;" readonly="true"   size="3"  maxlength="4" class="MiniTxtFiltro" value="<?=GetParameter("fecha_1_3")?>" type="text"><label for="fecha_1_3"></label></span>
				
				<span id="calendar_1"><img id="cal_img_1" src="images/calendar.gif" alt="Elige una fecha." class="AcomodaCalendario"></span>
				<script type="text/javascript">
					Calendar.setup({
					inputField	 : "fecha_1_3",
					baseField      : "fecha_1",
					displayArea    : "calendar_1",
					button		 : "cal_img_1",
					ifFormat	 : "%d/%m/%Y",  
					onSelect	 : selectEuropeDate
					});
				</script>
			</div>
			<div class="SeparaModulo">
				<small class="LabelSmallFiltro">Hasta:</small> 
				<span><input id="fecha_2_1" name="fecha_2_1" readonly="true" class="MiniTxtFiltro"  size="1"  maxlength="2" value="<?=GetParameter("fecha_2_1")?>" type="text"><label for="fecha_2_1"></label></span>
				<span><input id="fecha_2_2" name="fecha_2_2" readonly="true" class="MiniTxtFiltro"  size="1" maxlength="2" value="<?=GetParameter("fecha_2_2")?>" type="text"><label for="fecha_2_2"></label></span>
				<span><input id="fecha_2_3" name="fecha_2_3" readonly="true" style="width: 38px;" class="MiniTxtFiltro"  size="3"  maxlength="4" value="<?=GetParameter("fecha_2_3")?>" type="text"><label for="fecha_2_3"></label></span>
	
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
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
		<input class="botonFiltro" type="button" value="Cancelar" onclick="self.location='mistps.php';" name="btnVolver">
	</div>
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

	<? if($_SESSION["UserAccess"] == "P" || $_SESSION["UserAccess"] == "Y" || $_SESSION["UserAccess"] == "A"){?>
	<b><a class="boton" href="tpform.php?action=new&return_url=<?=$strReturnUrl?>">Crear Nuevo TP</a></b>
	<? }?>
	</div>
<div class="TableBgBig">
	<table border="0" cellspacing="0" cellpadding="0">
	<thead class="HeaderStyle">
		<tr>
		<th>Creado</th>
		<th>Fecha de entrega</th>
		<th>Comisi&oacute;n</th>
		<th>Modalidad</th>
		<th>T&iacute;tulo</th>
		<th>Archivo</th>
		<th align="center">Acci&oacute;n</th>
		</tr>
	</thead>
	<?
	$bHayDatos = false;
	if(mysql_num_rows($Result)){
		$bHayDatos = true;
		$ii = 0;
		while($oRs = mysql_fetch_array($Result)){
			
			$bActual = false;
			$mes = 0;
			$dia = 0;
			$anio = 0;
			$fecha_actual=date('d') . "/" . date('m') . "/" . date('Y');
			$fecha_operar=DateFormat($oRs["comision_fechadesde"]);
			if(compararFechas($fecha_operar, $fecha_actual) <= 0){
				$fecha_operar=DateFormat($oRs["comision_fechahasta"]);
				if(compararFechas($fecha_operar, $fecha_actual) >= 0){
					$bActual = true;
				}	
			}
			
			$iIdUsuario = $oRs["usuario_id"];
			$strCreado = DateTimeFormat($oRs["tp_fechaalta"]) . " por <strong>" . $oRs["usuario_nombre"] . "  " . $oRs["usuario_apellido"] . "</strong>";
			$strFechaEntrega = DateFormat($oRs["tp_fechaentrega"]);
			$strTitulo = $oRs["tp_titulo"];
			$strComision = $oRs["comision"];
			$strComisionCodigo = $oRs["comision_codigo"];
			$strMateria = $oRs["materia"];
			$strCarrera = $oRs["carrera"];
			$strFacultad = $oRs["facultad"];
			$strUniversidad = $oRs["universidad"];
			$strTipoUsuario = $oRs["usuario_tipo"];
			$strModalidad = "";
			switch($strTipoUsuario){
				case "P":
					$strModalidad = "Profesor";
					break;
				case "Y":
					$strModalidad = "Ayudante";
					break;
				case "L":
					$strModalidad = "Alumno";
					break;
			}
			$ii++;
			?>
			<tr class="<? if ($ii % 2  != 0){ ?>RowStyle<? }else{?>AltRowStyle<? }?>">
				<td class="TDleft"><?=$strCreado?></td>
				<td><?=$strFechaEntrega?></td>
				<td><b><?=$strUniversidad?></b> - <?=$strFacultad?> (<?=$strCarrera?>)<br /><?=$strMateria?> - <?=$strComision?><? if($strComisionCodigo != ""){?> (<?=$strComisionCodigo?>)<? }?></td>
				<td><?=$strModalidad?></td>
				<td><?=$strTitulo?></td>
				<td align="center"><a href="descarga.php?id=<?=encrypt($oRs["tp_id"])?>&t=T"><img src="images/download.png" title="Descargar" alt="Descargar" style="width:36px;border:0;"></a></td>
				<td class="TDright" width="140px;" ><a  href="tpver.php?id=<?=encrypt($oRs["tp_id"])?>&return_url=<?=$strReturnUrl?>">
				<img src="images/lupa.png" title="Ver Detalle" alt="Ver Detalle"  style="width:32px;border:0;"></a>
					<? if($strTipoUsuario == "P" || $strTipoUsuario == "Y"){?>
						<a  href="entregasverprofesor.php?idt=<?=encrypt($oRs["tp_id"])?>&return_url=<?=$strReturnUrl?>">
						<img src="images/documents.png" title="Ver Entregas" alt="Ver Entregas" style="width:32px;border:0;" /></a>
						<? if($bActual){?>
							<a href="tpform.php?id=<?=encrypt($oRs["tp_id"])?>&action=edit&return_url=<?=$strReturnUrl?>">
							<img src="images/lapiz.png" title="Editar" alt="Editar" style="width:30px;border:0;"/>	
							</a>
							<? if($iIdUsuario == $_SESSION["UserId"]){?>
								<a href="ConfirmDelete('tpform.php?btnBorrar=yes&id=<?=encrypt($oRs["tp_id"])?>&return_url=<?=url_encode($strReturnUrl)?>"><img src="images/trash_empty.png" title="Borrar" alt="Borrar" style="width:32px;border:0;" /></a>
							<? }?>
						<? }?>
					<? }else{?>
						
				<a  href="entregasver.php?idt=<?=encrypt($oRs["tp_id"])?>&return_url=<?=$strReturnUrl?>">
				<img src="images/documents.png" title="Ver Entregas" alt="Ver Entregas" style="width:32px;border:0;" /></a>
				<a  href="entregaform.php?idt=<?=encrypt($oRs["tp_id"])?>&action=new&return_url=<?=$strReturnUrl?>">
			<img src="images/subscribe_icon.png" title="Realizar Nueva Entrega" alt="Realizar Nueva Entrega" style="width:30px;border:0;" />	
			
	</a>
					
					<? }?>
				</td>
			</tr>
	<?	}?>
	<?
	}else{?>
		<tr>
			<td colspan="7" width="800" class="TDleftright"><b>No hay ning&uacute;n TP actualmente.</b></td>
		<tr>
	<?
	}
	/* Liberar conjunto de resultados */
	mysql_free_result($Result);?>
</table>
</div>
<br />
<div id="paginador" class="paginador" align="center">  
	<? if($bHayDatos){?>
		<?=$strPaginador?>
	<? }?>
</div>
<? 
include "common/inc_database_close.php";
include "footer.php"; ?>
