<?
session_start();
header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
header("Expires: -100000"); //Expira página

/* INCLUYO ARCHIVO DE CONFIGURACIÓN. */
include "includes/sitesettings_inc.php";
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
include "common/func_pagination_www.inc";

$RequireAccess = "A|L|P|Y";
include "access.php";

$iIdComision = "0";
if(GetParameter("idc") != "" && is_numeric(decrypt(GetParameter("idc")))){
	$iIdComision = decrypt(GetParameter("idc"));
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

$strParams = "fecha_1_1=" . GetParameter("fecha_1_1") . 
			 "&fecha_1_2=" . GetParameter("fecha_1_2") . 
			 "&fecha_1_2=" . GetParameter("fecha_1_2") . 
			 "&fecha_2_1=" . GetParameter("fecha_2_1") . 
			 "&fecha_2_2=" . GetParameter("fecha_2_2") . 
			 "&fecha_2_2=" . GetParameter("fecha_2_2") . 
			 "&cmbUniversidad=" . GetParameter("cmbUniversidad") . 
			 "&cmbFacultad=" . GetParameter("cmbFacultad") . 
			 "&idr=" . GetParameter("idr") .
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

//Borro al usuario de la comisión
if(GetParameter("btnBorrar") == "yes"){
	$strSQLComm = " UPDATE usuario_comision SET " .
				  " activo = 'N' " . 
				  " WHERE comision_id = " . SafeSql($iIdComision) .
				  " AND usuario_id = " . SafeSql($_SESSION["UserId"]);
	mysql_query($strSQLComm);
}

// Obtengo el listado de materias.
$strSQLComm = " SELECT U.usuario_nombre, U.usuario_apellido, C.comision_id, C.comision_codigo, C.comision_fechadesde, C.comision_fechahasta, U.tipo_usuario, UC.usuario_tipo, " . 
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
				" AND U.usuario_id = " . SafeSql($_SESSION["UserId"]);


if($iIdUniversidad != ""){
	$strSQLComm = $strSQLComm . " AND UN.universidad_id = " . SafeSql($iIdUniversidad);
}
if($iIdFacultad != ""){
	$strSQLComm = $strSQLComm . " AND F.facultad_id = " . SafeSql($iIdFacultad);
}
if($iIdCarrera != ""){
	$strSQLComm = $strSQLComm . " AND R.carrera_id = " . SafeSql($iIdCarrera);
}
if(GetParameter("fecha_1_1") != ""){
	$strFechaDesde = GetParameter('fecha_1_1') ."/". GetParameter('fecha_1_2') . "/". GetParameter('fecha_1_3');
	$strSQLComm = $strSQLComm . " AND C.comision_fechadesde >= " . SqlDate($strFechaDesde);
}
if(GetParameter("fecha_2_1") != ""){
	$strFechaHasta = GetParameter('fecha_2_1') ."/". GetParameter('fecha_2_2') . "/". GetParameter('fecha_2_3');
	$strSQLComm = $strSQLComm . " AND C.comision_fechahasta <= " . SqlDate($strFechaHasta);
}
if(GetParameter("rdRango") == "Rango"){
	$strRango = "Rango";
}else{
	$strSQLComm = $strSQLComm . " AND C.comision_fechadesde <= CURDATE() AND C.comision_fechahasta >= CURDATE() ";
	$strRango = "Actual";
}

$strSQLComm = $strSQLComm . " ORDER BY C.comision_fechadesde DESC ";

//" AND C.comision_fechadesde <= CURDATE() AND C.comision_fechahasta >= CURDATE() " .
			
// ******** PAGINADOR
$PagRowsPerPage = 20;
$strPaginador = PaginationImg($strSQLComm, $strParams);

if ( $PagAbsolutePosition >= 0 && $PagRowsPerPage >= 0 )
{
	$strSQLComm .= " LIMIT $PagAbsolutePosition, $PagRowsPerPage ";
}
// ******** FIN: PAGINADOR
$Result = mysql_query($strSQLComm);

//Seteo los default
if($iIdUniversidad == "")$iIdUniversidad = "0";
if($iIdFacultad == "") $iIdFacultad = "0";
if($iIdCarrera == "") $iIdCarrera= "0";

include "header.php"; 
?>
<script language="javascript" type="text/javascript">
	function Baja(id){
		if(confirm('Desea realmente darse de baja a la comision?')){
			document.getElementById("btnBorrar").value = "yes";
			document.getElementById("idc").value = id;
			window.document.forms['frmForm'].submit();
		}
	}
	
	function setearFechas(){
		var frmForm = window.document.forms['frmForm'];
		if(frmForm.rdRango[0].checked){
			document.getElementById("dvFechas").style.display = 'none';
			document.getElementById("fecha_1_1").value = "";
			document.getElementById("fecha_1_2").value = "";
			document.getElementById("fecha_1_3").value = "";
			document.getElementById("fecha_2_1").value = "";
			document.getElementById("fecha_2_2").value = "";
			document.getElementById("fecha_2_3").value = "";
		}else{
			document.getElementById("dvFechas").style.display = 'block';
		}
	}
</script>

<h2>Mis Materias</h2>

<div class="SpaceFiltroBusquedaBig">
<p class="NombreFiltro">Filtro de B�squeda</p><hr class="HRFiltro" align="left"/>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm" id="frmForm" class="Filtro">
	<input type="hidden" name="btnBorrar" id="btnBorrar" />
	<input type="hidden" name="idc" id="idc" />
	
	<div class="Filtro">
		<div class="SeparaFiltro">
			<div class="SeparaModulo">
				<input type="radio" name="rdRango" value="Actual" onclick="setearFechas();"<? if($strRango == "Actual"){?> checked="checked"<? }?>/> <span class="LabelSmallFiltro"><b>Comisiones Actuales</b></span>
			</div>
			<div class="SeparaModulo">
			<input type="radio" name="rdRango" value="Rango" onclick="setearFechas();"<? if($strRango == "Rango"){?> checked="checked"<? }?>/> <span class="LabelSmallFiltro"><b>Todos las Comisiones</b></span>
			</div>
		</div>
		<div class="SeparaFiltro" id="dvFechas">
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
				<div class="LabelFiltro">
					Universidad: 
				</div>
				<? ObtenerComboUniversidad($_SESSION["UserId"], $iIdUniversidad, "onchange=\"TraerFacultades('" . $iIdFacultad . "','--Todas--','Y', 'AcomodaDdlFiltroMedium');\"", "--Todas--", "Y", "AcomodaDdlFiltroMedium")?>&nbsp;
			</div>
			<div class="SeparaModulo">
				<div class="LabelFiltro">
					Facultad: 
				</div>
				<? ObtenerComboFacultad($_SESSION["UserId"], $iIdFacultad, $iIdUniversidad, "onchange=\"TraerCarreras('" . $iIdCarrera . "','--Todas--','Y', 'AcomodaDdlFiltroMedium');\"", "--Todas--", "Y", "AcomodaDdlFiltroMedium")?>
			</div>
			<div class="SeparaModulo">
				<div class="LabelFiltro">
					Carrera: 
				</div>
				<? ObtenerComboCarrera($_SESSION["UserId"], "", "0")?>
			</div>
		</div>
	</div>
	<div class="botonesFiltro">
		<input class="botonFiltro" type="submit" name="btnBuscar" value="Buscar">
		<input class="botonFiltro" type="button" value="Cancelar" onclick="self.location='mismaterias.php';" name="btnVolver">
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
	<a class="boton" href="comisioninscripcion.php?return_url=<?=$strReturnUrl?>">Cursar Nueva Materia</a>
	<?
	if($_SESSION["UserAccess"] == "P" || $_SESSION["UserAccess"] == "A"){?>
		<a class="boton" href="comisionform.php?action=new&return_url=<?=$strReturnUrl?>">Nueva Materia</a>
	<?
	}?>
	<a class="boton" href="misarchivos.php?return_url=<?=$strReturnUrl?>">Archivos</a>
	</div>
	<div class="TableBgBig">
		<table border="0" cellspacing="0" cellpadding="0" width="970px">
		<thead class="HeaderStyle">
			<tr>
			<th>Periodo</th>
			<th>Comisi&oacute;n</th>
			<th>Modalidad</th>
			<th>Acci&oacute;n</th>
			</tr>
		</thead>
		<?
		$bHayDatos = false;
		if(mysql_num_rows($Result)){
			$bHayDatos = true;
			$ii = 0;
			while($oRs = mysql_fetch_array($Result)){
			
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
				$ii++;
				?>
				<tr class="<? if ($ii % 2  != 0){ ?>RowStyle<? }else{?>AltRowStyle<? }?>">
					<td class="TDleft"><?=$strPeriodo?></td>
					<td><b><?=$strUniversidad?></b> - <?=$strFacultad?> (<?=$strCarrera?>)<br /><?=$strMateria?> - <?=$strComision?><? if($strComisionCodigo != ""){?> (<?=$strComisionCodigo?>)<? }?></td>
					<td><?=$strTipoUsuario?></td>
					<td class="TDright">
<a href="comisiondetalle.php?id=<?=encrypt($oRs["comision_id"])?>&return_url=<?=$strReturnUrl?>">
<img src="images/lupa.png" title="Ver Contenido" alt="Ver Contenido" style="width: 30px; border:0;"/></a>
<a href="mismensajes.php?idc=<?=encrypt($oRs["comision_id"])?>&return_url=<?=$strReturnUrl?>">
<img src="images/envelope.png" title="Ver Mensajes" alt="Ver Mensajes" style="width: 30px; border:0;"/></a>
<a href="misarchivos.php?idc=<?=encrypt($oRs["comision_id"])?>&return_url=<?=$strReturnUrl?>">
<img src="images/archivos.png" title="Ver Archivos" alt="Ver Archivos" style="width: 30px; border:0;"/>
<!--	<img src="images/documents.png" title="Ver Archivos" alt="Ver Archivos" style="width: 30px; border:0;"/> --></a>
<a href="javascript://;" onclick="Baja('<?=encrypt($oRs["comision_id"])?>');">
<img src="images/desuscribirse.png" title="Darme de Baja" alt="Darme de Baja" style="width: 30px; border:0;"/></a>
</td>
				</tr>
		<?	}?>
		<?
		}else{?>
			<tr>
				<td colspan="7" width="800" class="TDleftright"><b>No est&aacute; inscripto en ninguna materia actualmente.</b></td>
			<tr>
		<?
		}
		/* Liberar conjunto de resultados */
		mysql_free_result($Result);?>
	</table>
	</div>
<br/>
<div id="paginador" class="paginador">  
<? if($bHayDatos){?>
		<?=$strPaginador?>
	<? }?>
</div>

<? 
include "common/inc_database_close.php";
include "footer.php";
?>
<script language="javascript" type="text/javascript">
	window.onload=function(){
	<? if($iIdUniversidad != ""){?>
			TraerFacultades('<?=$iIdFacultad?>', '--Todas--', 'Y');
		<? }
		 if($iIdFacultad != ""){?>
			TraerCarreras('<?=$iIdCarrera?>', '--Todas--', 'Y');
		<? }?>
		setearFechas();

	}
</script>
