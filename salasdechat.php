<?
session_start();
header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
header("Expires: -100000"); //Expira pÃ¡gina

/* INCLUYO ARCHIVO DE CONFIGURACIÃ“N. */
include "includes/sitesettings_inc.php";
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
include "common/func_pagination_www.inc";
		
$RequireAccess = "A|L|P|Y";
include "access.php";
include "common/inc_database_open.php";
$strParams = '';
//Consulta SQL para traer las materias en la que estoy inscripto

$strSQLComm = " SELECT C.comision_id, C.comision_codigo, C.comision_titulo AS comision, M.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad " .
				" FROM usuario_comision AS UC " . 
				" INNER JOIN usuario AS U ON U.usuario_id = UC.usuario_id " . 
				" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
				" INNER JOIN materia AS M ON (M.materia_id = C.materia_id AND M.activo = 'Y') " . 
				" INNER JOIN carrera AS R ON (R.carrera_id = M.carrera_id AND R.activo = 'Y') " . 
				" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
				" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
				" WHERE U.activo = 'Y' " .
				" AND UC.activo = 'Y' " .
				" AND C.comision_fechahasta >= CURDATE()" .
				" AND U.usuario_id = " . SafeSql($_SESSION["UserId"]);


// ******** PAGINADOR
$PagRowsPerPage = 5;
$strPaginador = PaginationImg($strSQLComm, $strParams);

if ( $PagAbsolutePosition >= 0 && $PagRowsPerPage >= 0 )
{
	$strSQLComm .= " LIMIT $PagAbsolutePosition, $PagRowsPerPage ";
}
// ******** FIN: PAGINADOR
$Result = mysql_query($strSQLComm);

include "header.php"; 
?>
<h2>Salas de Chat</h2>

<p style="margin-left: 20px;">En el chat de Virtual U podrás encontrar distintas salas según tus necesidades.
Espacio para hacer consultas varias a profesores, consultas sobre TPs específicos, podrás chatear entre colgeas en la sala de alumnos
o de profesores o también acceder a la sala general donde podrás hacer contactos interesantes y pasar un buen rato. 
Además, encuentra lo último en la sala Noticias. <b>¡Entra ya!</b>
</p>


<table border="0" width="900px" align="center">
<tr style="height: 176px;">
<td width="33%" style="background: url('http://www.gibaless.com.ar/virtualuphp/images/salageneral.png') no-repeat;">
<div style="margin-left: 40px; margin-top: 100px;">
<a href="./chat/SaladeChat_General.php" style="font-size: 11px;">Accede a esta sala</a>
<div>
</td>
<td width="33%" style="background: url('http://www.gibaless.com.ar/virtualuphp/images/salanoticias.png') no-repeat;">
<div style="margin-left: 40px; margin-top: 100px;">
<a href="./chat/SaladeChat_Noticias.php" style="font-size: 11px;">Accede a esta sala</a>
<div>
</td>
<td width="33%" style="background: url('http://www.gibaless.com.ar/virtualuphp/images/salaconsultas.png') no-repeat;">
<div style="margin-left: 40px; margin-top: 100px;">
<a href="./chat/SaladeChat_Consultas.php" style="font-size: 11px;">Accede a esta sala</a>
<div>
</td>
</tr>
<tr style="height: 176px;">
<td width="33%" style="background: url('http://www.gibaless.com.ar/virtualuphp/images/salaalumnos.png') no-repeat;">
<div style="margin-left: 40px; margin-top: 100px;">
<a href="./chat/SaladeChat_Alumnos.php" style="font-size: 11px;">Accede a esta sala</a>
<div>
</td>
<td width="33%" style="background: url('http://www.gibaless.com.ar/virtualuphp/images/salaprofesores.png') no-repeat;">
<div style="margin-left: 40px; margin-top: 100px;">
<a href="./chat/SaladeChat_Profesores.php" style="font-size: 11px;">Accede a esta sala</a>
<div>
</td>
<td width="33%" style="background: url('http://www.gibaless.com.ar/virtualuphp/images/salaentregas.png') no-repeat;">
<div style="margin-left: 40px; margin-top: 100px;">
<a href="./chat/SaladeChat_Entregas.php" style="font-size: 11px;">Accede a esta sala</a>
<div>
</td>
</tr>
</table>

 <h2>Salas de Chat de Mis Materias</h2>
<?
$bHayDatos = "";
if(mysql_num_rows($Result)){
	$bHayDatos = true;
	while($oRs = mysql_fetch_array($Result)){

	$iIdComision = $oRs["comision_id"];
	$strComision = $oRs["comision"];
	$strComisionCodigo = $oRs["comision_codigo"];
	$strMateria = $oRs["materia"];
	$strCarrera = $oRs["carrera"];
	$strFacultad = $oRs["facultad"];
	$strUniversidad = $oRs["universidad"];

 ?>
<div style="margin-left: 30px; width: 700px; ">
	<p style="font-size: 12px;"><b><?=$strUniversidad?></b> <img src="images/icon-next.gif"/> <b><?=$strFacultad?></b> <img src="images/icon-next.gif"/> <b><?=$strCarrera?></b><br/><img src="images/chat-icon.png"/>
	<a href="chat/SaladeChat_Comisiones.php?idc=<?=encrypt($iIdComision)?>" style="margin-left: 10px;" >
	<b><?=$strMateria?></b> : <?=$strComision?> (<?=$strComisionCodigo?>)</a>
</div>
<?	
	}//end of while

}// end of if
?>
		<div id="paginador" class="paginador" style="width:700px; margin-top: 20px;" >  
			<? if($bHayDatos){?>
				<?=$strPaginador?>
			<? }?>
		</div>

<?
include "common/inc_database_close.php";
include "footer.php";
?>
