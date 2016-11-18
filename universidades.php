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

$strParams = "";

// ARMO RETURN URL
$strReturnUrl = $_SERVER["SCRIPT_NAME"];
if(isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] != ""){
	$strReturnUrl .=  "?" . $_SERVER["QUERY_STRING"];
}else {
	$strReturnUrl .=  "?" . $strParams;
}
$strReturnUrl = urlencode($strReturnUrl);

include "common/inc_database_open.php";

// Obtengo el listado de universidades.
$strSQLCommUniversidades = " SELECT U.*, PE.estado, P.pais_titulo " .
				" FROM universidad AS U " . 
				" INNER JOIN provincias_estados AS PE ON U.prov_id = PE.prov_id " . 
				" INNER JOIN pais AS P ON PE.pais_id = P.pais_id " . 
				" WHERE U.activo = 'Y' " .
				" AND P.activo = 'Y' " .
				" ORDER BY PE.estado, P.pais_titulo, U.universidad_titulo ";

// ******** PAGINADOR
$PagRowsPerPage = 16;
$strPaginador = PaginationImg($strSQLCommUniversidades, $strParams);

if ( $PagAbsolutePosition >= 0 && $PagRowsPerPage >= 0 )
{
	$strSQLCommUniversidades .= " LIMIT $PagAbsolutePosition, $PagRowsPerPage ";
}
// ******** FIN: PAGINADOR
$Result = mysql_query($strSQLCommUniversidades);

include "header.php"; 
?>
<h2>Universidades</h2>
<div class="SubMenu">
<a class="boton" href="comisioninscripcion.php?return_url=<?=$strReturnUrl?>">Inscribirme a una Materia</a>
<?
if(isset($_SESSION["UserAccess"]) && ($_SESSION["UserAccess"] == "P" || $_SESSION["UserAccess"] == "Y" || $_SESSION["UserAccess"] == "A")){?>
	<a class="boton" href="universidadform.php?action=new&return_url=<?=$strReturnUrl?>">Crear Nueva Universidad</a>
<?
}?>
</div>
<div class="TableBgBig">
	<table border="0" cellspacing="0" cellpadding="0" width="950px" >
	<thead class="HeaderStyle">
			<th width="70px">Pa&iacute;s</th>
			<th width="80px">Provincia</th>
			<th>Universidad</th>
			<th>Tel&eacute;fono</th>
			<th>Email</th>
			<th>Web</th>
			<th width="100px">Acci&oacute;n</th>
	</thead>
	<tbody>
	<?
	$bHayDatos = false;
	if(mysql_num_rows($Result)){
		$bHayDatos = true;
		$ii = 0;
		while($oRs = mysql_fetch_array($Result)){
		
			$ii++;
			?>
			<tr class="<? if ($ii % 2  != 0){ ?>RowStyle<? }else{?>AltRowStyle<? }?>" >
				<td class="TDleft"><?=$oRs["pais_titulo"]?></td>
				<td><?=$oRs["estado"]?></td>
				<td><?=$oRs["universidad_titulo"]?></td>
				<td><?=$oRs["universidad_telefono"]?></td>
				<td><? if($oRs["universidad_email"] != ""){?><a href="mailto:<?=$oRs["universidad_email"]?>" target="_blank"><?=$oRs["universidad_email"]?></a><? }?></td>
				<td><? if($oRs["universidad_web"] != ""){?><a href="http://<?=$oRs["universidad_web"]?>" target="_blank"><?=$oRs["universidad_web"]?></a><? }?></td>
					<td><a href="universidadver.php?id=<?=encrypt($oRs["universidad_id"])?>&return_url=<?=$strReturnUrl?>">
					<img src="images/lupa.png" title="Detalle" alt="Detalle" style="border:0; width: 30px;"/></a>
					<a href="comisioninscripcion.php?idu=<?=encrypt($oRs["universidad_id"])?>&return_url=<?=$strReturnUrl?>">
					<img src="images/subscribe_icon.png" title="Inscribirme a una Materia" alt="Inscribirme a una Materia" style="border:0; width: 30px;"/></a>
					<? if(isset($_SESSION["UserAccess"]) && ($_SESSION["UserAccess"] == "P" || $_SESSION["UserAccess"] == "Y"  || $_SESSION["UserAccess"] == "A")){?>
					<a href="comisionform.php?idu=<?=encrypt($oRs["universidad_id"])?>&action=new&return_url=<?=$strReturnUrl?>">
					<img src="images/nueva comision.png" title="Crear Nueva Comisi&oacute;n" alt="Crear Nueva Comisi&oacute;n" style="border:0; width: 30px;"/></a>
					<?
					}?>
				</td>
			</tr>
	<?	}?>
	<?
	}else{?>
		<tr>
			<td colspan="7"><b>No hay ninguna universidad listada</b></td>
		</tr>
	<?
	}
	/* Liberar conjunto de resultados */
	mysql_free_result($Result);?>
	</tbody>
	</table>
</div>


<div id="paginador" class="paginador" align="center">  
	<? if($bHayDatos){?>
		<?=$strPaginador?>
	<? }?>
</div>
<? 
include "common/inc_database_close.php";
include "footer.php"; ?>