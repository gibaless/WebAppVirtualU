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

$iIdTp = "0";
if(GetParameter("idt") != "" && is_numeric(decrypt(GetParameter("idt")))){
	$iIdTp = decrypt(GetParameter("idt"));
}


$strParams = "fecha_1_1=" . GetParameter("fecha_1_1") . 
			 "&fecha_1_2=" . GetParameter("fecha_1_2") . 
			 "&fecha_1_2=" . GetParameter("fecha_1_2") . 
			 "&fecha_2_1=" . GetParameter("fecha_2_1") . 
			 "&fecha_2_2=" . GetParameter("fecha_2_2") . 
			 "&fecha_2_2=" . GetParameter("fecha_2_2") .
			 "&cmbEstado=" . GetParameter("cmbEstado") . 
			 "&idt=" . GetParameter("idt");


// ARMO RETURN URL
$strReturnUrl = $_SERVER["SCRIPT_NAME"];
if(isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] != ""){
	$strReturnUrl .=  "?" . $_SERVER["QUERY_STRING"];
}else {
	$strReturnUrl .=  "?" . $strParams;
}
$strReturnUrl = urlencode($strReturnUrl);

include "common/inc_database_open.php";

$strTP = "";

//Obtengo el nombre del TP
$strSQLCommTP = " SELECT TP.tp_titulo " .
				" FROM trabajo_practico AS TP " .
				" WHERE TP.activo = 'Y' " . 
				" AND TP.tp_id = " . SafeSql($iIdTp);
$Result = mysql_query($strSQLCommTP);
if(mysql_num_rows($Result)){
	while($oRs = mysql_fetch_array($Result)){
		$strTP = '"' . $oRs["tp_titulo"] . '"';
	}
}
/* Liberar conjunto de resultados */
mysql_free_result($Result);
	
	
// Obtengo el listado de entregas.
$strSQLCommEntregas = " SELECT DISTINCT TP.tp_titulo, E.tp_id, E.entrega_id, E.grupo_id, E.entrega_version, E.entrega_archivo, E.entrega_observacion, E.entrega_fechacreacion, " .
				" E.entrega_fechaentrega, E.entrega_estado, E.activo, N.nota_nota, UR.usuario_nombre, UR.usuario_apellido, G.grupo_nombre, G.grupo_id " . 
				" FROM entrega AS E " . 
				" INNER JOIN trabajo_practico AS TP ON (E.tp_id = TP.tp_id AND TP.activo = 'Y') " . 
				" INNER JOIN usuario_grupo AS UG ON (UG.grupo_id = E.grupo_id AND UG.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
				" INNER JOIN grupo AS G ON (G.grupo_id = UG.grupo_id AND G.activo = 'Y') " . 
				" INNER JOIN usuario_comision AS UC ON (UC.comision_id = G.comision_id AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
				" LEFT JOIN nota AS N ON N.entrega_id = E.entrega_id " . 
				" LEFT JOIN usuario AS UR ON (UR.usuario_id = N.usuario_id AND UR.activo = 'Y') " . 
				" WHERE UC.activo = 'Y' " .
				" AND E.activo = 'Y' " .
				" AND TP.tp_id = " . SafeSql($iIdTp);
				
if(GetParameter("fecha_1_1") != ""){
	$strFechaDesde = GetParameter('fecha_1_1') ."/". GetParameter('fecha_1_2') . "/". GetParameter('fecha_1_3');
	$strSQLCommEntregas = $strSQLCommEntregas . " AND E.entrega_fechaentrega >= " . SqlDateTime($strFechaDesde . " 00:00:00");
}
if(GetParameter("fecha_2_1") != ""){
	$strFechaHasta = GetParameter('fecha_2_1') ."/". GetParameter('fecha_2_2') . "/". GetParameter('fecha_2_3');
	$strSQLCommEntregas = $strSQLCommEntregas . " AND E.entrega_fechaentrega <= " . SqlDateTime($strFechaHasta . " 23:59:59");
}
if(GetParameter("cmbEstado") != ""){
	$strSQLCommEntregas = $strSQLCommEntregas . " AND E.entrega_estado = '" . SafeSql(GetParameter("cmbEstado")) . "'";
}
$strSQLCommEntregas = $strSQLCommEntregas . " ORDER BY E.entrega_version DESC ";

// ******** PAGINADOR
$PagRowsPerPage = 20;
$strPaginador = PaginationImg($strSQLCommEntregas, $strParams);

if ( $PagAbsolutePosition >= 0 && $PagRowsPerPage >= 0 )
{
	$strSQLCommEntregas .= " LIMIT $PagAbsolutePosition, $PagRowsPerPage ";
}
// ******** FIN: PAGINADOR
$Result = mysql_query($strSQLCommEntregas);

include "header.php"; 
?>
<h2>Entregas del TP <?=$strTP?></h2>
<div class="SpaceFiltroBusquedaBig">
<p class="NombreFiltro">Filtro de Búsqueda</p><hr class="HRFiltro" align="left"/>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm">
	<input type="hidden" name="idt" id="idt" value="<?=GetParameter("idt")?>" />
	
	<div class="Filtro">
		<div class="SeparaFiltro">
			<div class="SeparaModulo">
				<div class="LabelFiltro">
					Fecha de Entrega:&nbsp;&nbsp;
				</div>
				<small>Desde:</small> 
				<span><input id="fecha_1_1" name="fecha_1_1" readonly="true" class="element text" size="1"  maxlength="2" value="<?=GetParameter("fecha_1_1")?>" type="text"><label for="fecha_1_1"></label></span>
				<span><input id="fecha_1_2" name="fecha_1_2" readonly="true" class="element text" size="1" maxlength="2" value="<?=GetParameter("fecha_1_2")?>" type="text"><label for="fecha_1_2"></label></span>
				<span><input id="fecha_1_3" name="fecha_1_3" readonly="true" class="element text" size="3"  maxlength="4" value="<?=GetParameter("fecha_1_3")?>" type="text"><label for="fecha_1_3"></label></span>
				
				<span id="calendar_1"><img id="cal_img_1" class="datepicker" src="images/calendar.gif" alt="Elige una fecha." ></span>
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
				<small>Hasta:</small> 
				<span><input id="fecha_2_1" name="fecha_2_1" readonly="true" class="element text" size="1"  maxlength="2" value="<?=GetParameter("fecha_2_1")?>" type="text"><label for="fecha_2_1"></label></span>
				<span><input id="fecha_2_2" name="fecha_2_2" readonly="true" class="element text" size="1" maxlength="2" value="<?=GetParameter("fecha_2_2")?>" type="text"><label for="fecha_2_2"></label></span>
				<span><input id="fecha_2_3" name="fecha_2_3" readonly="true" class="element text" size="3"  maxlength="4" value="<?=GetParameter("fecha_2_3")?>" type="text"><label for="fecha_2_3"></label></span>
	
				<span id="calendar_2"><img id="cal_img_2" class="datepicker" src="images/calendar.gif" alt="Elige una fecha." ></span>
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
					&nbsp;&nbsp;Estado:&nbsp;&nbsp;
				</div> 
				<select name="cmbEstado">
					<option value="">--Indistinto--</option>
					<option value="E"<? if(GetParameter("cmbEstado") == "E"){?> selected="selected"<? }?>>Entregado</option>
					<option value="C"<? if(GetParameter("cmbEstado") == "C"){?> selected="selected"<? }?>>Corregido</option>
				</select>
			</div>
		</div>
	</div>
	<div class="botonesFiltro">
		<input class="botonFiltro" type="submit" name="btnBuscar" value="Buscar">
		<input class="botonFiltro" type="button" value="Cancelar" onclick="self.location='entregasver.php?idt=<?=GetParameter("idt")?>';" name="btnVolver">
	</div>
</form>
<script language="javascript" type="text/javascript">
var frmvalidator2  = new Validator("frmForm");
frmvalidator2.EnableMsgsTogether();
frmvalidator2.setAddnlValidationFunction(ChequeaFechasFiltroIncorrectas);
</script>
</div>
<div class="SubMenu">
<? if($iIdTp != 0){?>
	<a class="boton" href="entregaform.php?idt=<?=encrypt($iIdTp)?>&action=new&return_url=<?=$strReturnUrl?>">Nueva Entrega</a>
<? }?>
</div>
<div class="TableBgBig">
	<table border="0" cellspacing="0" cellpadding="0">
	<thead class="HeaderStyle">
		<tr>
		<th>TP</th>
		<th>Nº<br />Entrega</th>
		<th>Fecha de entrega</th>
		<th>Grupo</th>
		<th>Archivo</th>
		<th>Estado</th>
		<th>Nota</th>
		<th>Acci&oacute;n</th>
		</tr>
	</thead>
	<?
	$bHayDatos = false;
	if(mysql_num_rows($Result)){
		$bHayDatos = true;
		$ii = 0;
		while($oRs = mysql_fetch_array($Result)){
			
			$strTitulo = $oRs["tp_titulo"];
			$strFechaEntrega = DateTimeFormat($oRs["entrega_fechaentrega"]);
			$strGrupo = $oRs["grupo_nombre"];
			$strVersion = $oRs["entrega_version"];
			$strArchivo = $oRs["entrega_archivo"];
			$strEstado = $oRs["entrega_estado"];
			if($strEstado == "C"){
				$strEstado = "Corregido";
				$strNota = $oRs["nota_nota"] . " por <strong>" . $oRs["usuario_nombre"] . "  " . $oRs["usuario_apellido"] . "</strong>";
			}else{
				$strEstado = "Entregado";
				$strNota = "No Corregido";
			}
			$strIntegrantes = "";
			//Listo los integrantes del grupo
			$strSQLCommDatosGrupo = "SELECT DISTINCT UR.usuario_nombre, UR.usuario_apellido, UR.usuario_dni, T.tipodni_titulo " . 
									" FROM usuario_grupo AS UG " . 
									" INNER JOIN usuario AS UR ON (UR.usuario_id = UG.usuario_id AND UR.activo = 'Y') " . 
									" INNER JOIN tipodni AS T ON T.tipodni_id = UR.tipodni_id " . 
									" WHERE UG.grupo_id = " . SafeSql($oRs["grupo_id"]);
			$ResultGrupo = mysql_query($strSQLCommDatosGrupo);
			if(mysql_num_rows($ResultGrupo)){  
					while ($oRGs = mysql_fetch_array($ResultGrupo)){
					$strIntegrantes .= $oRGs['usuario_apellido'] .", ". $oRGs['usuario_nombre'] . " (". $oRGs['tipodni_titulo'] . ":" . $oRGs['usuario_dni']. ")". "<br>";
					}
			}
			mysql_free_result($ResultGrupo);
			
			$ii++;
			?>
			<tr class="<? if ($ii % 2  != 0){ ?>RowStyle<? }else{?>AltRowStyle<? }?>">
				<td class="TDleft"><?=$strTitulo?></td>
				<td align="center"><?=$strVersion?></td>
				<td><?=$strFechaEntrega?></td>
				<td><a href="#" class="tooltip" style="text-decoration:none;"><b><?=$strGrupo?></b><span><?=$strIntegrantes?></span></a></td>
				<td><a href="descarga.php?id=<?=encrypt($oRs["entrega_id"])?>&t=E"><img src="images/download.png" title="Descargar" alt="Descargar" style="width:34px;border:0;"/></a></td>
				<td><b><?=$strEstado?></b></td>
				<td><?=$strNota?></td>
				<td class="TDright">
					<? if($oRs["entrega_estado"] == "C"){?>
						<a  href="corregirtp.php?id=<?=encrypt($oRs["entrega_id"])?>&return_url=<?=$strReturnUrl?>"><img src="images/lupa.png" title="Ver corrección" alt="Ver corrección" style="width:28px;border:0;"/></a>
						<a  href="entregaform.php?idt=<?=encrypt($oRs["tp_id"])?>&action=new&return_url=<?=$strReturnUrl?>"><img src="images/subscribe_icon.png" title="Reentregar" alt="Reentregar" style="width:28px;border:0;"/></a>
					<? }?>
				</td>
			</tr>
	<?	}?>
	<?
	}else{?>
		<tr>
			<td colspan="8" width="800" class="TDleftright"><b>No hay ninguna entrega para este TP actualmente.</b></td>
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
