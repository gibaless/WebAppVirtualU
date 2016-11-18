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
ini_set('display_errors', 1);
error_reporting(E_ALL);
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

include "header.php"; 
include "common/inc_database_open.php";

// Obtengo el listado de materias.
$strSQLComm2 = " SELECT U.usuario_id, C.comision_id, M.materia_id, R.carrera_id, F.facultad_id, UN.universidad_id, UC.usuario_tipo " .
		" FROM usuario_comision AS UC " . 
		" INNER JOIN usuario AS U ON U.usuario_id = UC.usuario_id " . 
		" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
		" INNER JOIN materia AS M ON (M.materia_id = C.materia_id AND M.activo = 'Y') " . 
		" INNER JOIN carrera AS R ON (R.carrera_id = M.carrera_id AND R.activo = 'Y') " . 
		" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
		" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
		" WHERE U.activo = 'Y' " .
		" AND UC.activo = 'Y' " .
		" AND UC.usuario_tipo IN ('P','Y','A') " . 
		" AND C.comision_fechahasta <= CURDATE() " .
		" AND U.usuario_id = " . SafeSql($_SESSION["UserId"]);

$Result = mysql_query($strSQLComm2);
?>
<h2>Mi Calendario</h2>
<script src="js/lib/prototype.js" type="text/javascript"></script>
<script src="js/src/scriptaculous.js" type="text/javascript"></script>
<script type="text/javascript">

	function highlightCalendarCell(element) {
		$(element).style.background = 'url(images/bg_today_high.jpg) repeat-y';
		$(element).style.border = '1px solid #bbb';
	}
	

	function resetCalendarCell(element) {
		$(element).style.border = '1px dotted #62A9FF';
		$(element).style.background = 'url(images/bg_day.png) repeat-x';
	}
	
	function startCalendar(month, year) {
		new Ajax.Updater('calendarInternal', 'ajax_calendario.php', {method: 'post', postBody: 'action=startCalendar&month='+month+'&year='+year+''});
	}
	
	function showEventForm(day, month, year) {
		
		//displayEvents(day, $F('ccMonth'), $F('ccYear'));
		var ele = document.getElementById("Eventos");
		var formevento = document.getElementById("NuevoEvento");
		
		ele.style.display = "none";
		formevento.style.display = "block";
		if(day) document.getElementById("evento_fecha_1_1").value = day;
		if(month) document.getElementById("evento_fecha_1_2").value = month;
		if(year) document.getElementById("evento_fecha_1_3").value = year;
		
	}
	
	function displayEvents(day, month, year) {
		var ele = document.getElementById("Eventos");
		var nevento = document.getElementById("NuevoEvento");
		ele.style.display = "block";
		nevento.style.display = "none";
		
		new Ajax.Updater('Eventos', 'ajax_calendario.php', {method: 'post', postBody: 'action=eventList&d='+day+'&m='+month+'&y='+year+''});
		if(Element.visible('Eventos')) {
			// do nothing, its already visble.
		} else {
			setTimeout("Element.show('Eventos')", 300);
		}
		

	}	
	function HidedisplayEvents() {
		Element.hide('NuevoEvento');
		// document.getElementById("Eventos").style. display="";
	}

	function addEvent() {
		
		var idComision = document.getElementById("cmbComision").value;  
		var day = document.getElementById("evento_fecha_1_1").value;
		var month = document.getElementById("evento_fecha_1_2").value;
		var year = document.getElementById("evento_fecha_1_3").value;
		var horainicio = document.getElementById("hora_evento").value; 
		var mininicio = document.getElementById("minuto_evento").value;  
		var horafin = document.getElementById("hora_evento_fin").value;  		
		var minfin = document.getElementById("minuto_evento_fin").value;  
		var descripcion =  document.getElementById("txtDescEvento").value;   
		var titulo = document.getElementById("txtTituloEvento").value;  
		var ubicacion = document.getElementById("txtUbicacion").value; 

		if(day && month && year && titulo) {
			new Ajax.Request('ajax_calendario.php', {method: 'post', postBody: 'action=addEvent&d='+day+'&m='+month+'&y='+year+'&t='+titulo+'&desc='+descripcion+'&ub='+ubicacion+'&min='+mininicio+'&hora='+horainicio+'&minfin='+minfin+'&horafin='+horafin+'&com='+idComision+'', onSuccess: highlightEvent(day)});	
			} 	
	}	
	

	function highlightEvent(day) {
		$('calendarDay_'+day+'').style.background = '#357EC7';
		alert("Evento creado exitosamente.");
		window.location.reload();
		//startCalendar(0,0);
		Element.hide('NuevoEvento');
	}
	
	
	function deleteEvent(eid) {
		confirmation = confirm('Confirma que desea borrar este evento del calendario?');
		if(confirmation == true) {
			new Ajax.Request('ajax_calendario.php', {method: 'post', postBody: 'action=deleteEvent&eid='+eid+'', onSuccess: Element.hide('event_'+eid+'')});
			startCalendar(0,0);
		} else {
			// Do not delete it!.
		}
	}
	

function validarFormCal(thisForm)
{

	if(thisForm.cmbUniversidad.value == ''){
			alert('Debe seleccionar una universidad.');
			thisForm.cmbUniversidad.focus();
			return false;
		}
	if(thisForm.cmbFacultad.value == ''){
			alert('Debe seleccionar una facultad.');
			thisForm.cmbFacultad.focus();
			return false;
		}
	if(thisForm.cmbCarrera.value == ''){
			alert('Debe seleccionar una carrera.');
			thisForm.cmbCarrera.focus();
			return false;
		}
	if(thisForm.cmbMateria.value == ''){
			alert('Debe seleccionar una materia.');
			thisForm.cmbMateria.focus();
			return false;
		}
	if(thisForm.cmbComision.value == ''){
			alert('Debe ingresar la comision.');
			thisForm.cmbComision.focus();
			return false;
		}
	if(thisForm.evento_fecha_1_1.value == ''){
			alert('Debe ingresar la fecha del evento.');
			thisForm.evento_fecha_1_1.focus();
			return false;
		}


  if(false == ChequeaFechasCorrectas())
  {
    alert("Debe ingresar una fecha de inicio válida (no anterior al día de hoy).");
     thisForm.evento_fecha_1_1.focus();
	return false;
  }


  if(false == ChequeaDiadeHoyCorrecto())
  {
    alert("Este evento no puede crearse en el pasado! Debe ingresar una hora válida.");
    thisForm.hora_evento.focus();
    return false;
  }


  if(false == ChequeaHorasCorrectas())
  {
    alert("Debe ingresar una hora de inicio y de fin válidas.");
    thisForm.minuto_evento.focus();	
    return false;
  }

   if(thisForm.txtTituloEvento.value == ''){
	alert('Debe ingresar el título del evento.');
	thisForm.txtTituloEvento.focus();
	return false;
  }

	addEvent();
       return true;
   
}
	
	
</script>

	<div id="calendar" class="calendarBox"><div id="calendarInternal"> &nbsp;</div> <br style="clear: both;">	</div>
	<script type="text/javascript">
	window.onload=function(){
		startCalendar(0,0); 
	}
	</script>
						
	<div id="Eventos" class="calendarEventos"> 
		<div style="text-align:right; margin-right: 2px;"><a href="#" onClick="showEventForm();" class="boton">Crear Nuevo Evento</a> </div>
		<div id="displayEvent">  </div>	
	</div>
	
	<div id="NuevoEvento" class="calendarEventoNuevo" > 
				
		<div style="text-align:right;">
		<a href="" onClick="CerrarNuevoEventoForm();">
		<img src="images/close.png" title="Cerrar" alt="cerrar" style="width: 26px; border:0; "/>

		</a></div>
		<div id="FormNuevoEvent">  <h3 style="margin-left: 15px; margin-top: -10px;">Nuevo Evento</h3> 
		<form name="frmEventoForm" id="frmEventoForm" onSubmit="return validarFormCal(this);">
		
		<table style="margin-left: 20px;" style="font-weight:bold;font-siez:12px;">
		<tr>
		<td align="right" style="font-size:12px;font-weight:bold;"><label>Universidad: (*)</label> </td> 
			<td>	<? ObtenerComboUniversidad($_SESSION["UserId"], $iIdUniversidad, "onchange=\"TraerFacultades('" . $iIdFacultad . "','--Seleccionar--','Y');\"","--Seleccionar--", "Y", "AcomodaDdlFiltroMedium")?>
			</td>
		</tr>
		<tr><td align="right" style="font-size:12px;font-weight:bold;"><label>Facultad: (*)</label></td>
			<td><? ObtenerComboFacultad($_SESSION["UserId"], $iIdFacultad, $iIdUniversidad, "onchange=\"TraerCarreras('" . $iIdCarrera . "','--Seleccionar--','Y');\"","--Seleccionar--", "Y", "AcomodaDdlFiltroMedium")?>
		</td></tr>
		<tr><td align="right" style="font-size:12px;font-weight:bold;"><label>Carrera: (*)</label></td>
			<td><? ObtenerComboCarrera($_SESSION["UserId"], $iIdCarrera, $iIdFacultad, "onchange=\"TraerMaterias('" . $iIdMateria . "','--Seleccionar--','Y');\"","--Seleccionar--", "Y", "AcomodaDdlFiltroMedium")?>
			</td>
		</tr>
		<tr><td align="right" style="font-size:12px;font-weight:bold;"><label>Materia: (*)</label></td>
			<td><? ObtenerComboMateria($_SESSION["UserId"], $iIdMateria, $iIdCarrera, "onchange=\"TraerComisiones('" . $iIdComision . "','--Seleccionar--','Y');\"","--Seleccionar--", "Y", "AcomodaDdlFiltroMedium")?>
		</td></tr>
		<tr><td align="right" style="font-size:12px;font-weight:bold;"><label>Comision: (*)</label></td>
			<td><? ObtenerComboComision($_SESSION["UserId"], $iIdComision, $iIdMateria, "","--Seleccionar--","Y", "AcomodaDdlFiltroMedium")?>
		</td>
		</tr>
		<tr><td align="right" style="font-size:12px;font-weight:bold;"><label for="evento_fecha_1_1">Fecha Inicio: (*)</label></td>
		<td>
			<span><input id="evento_fecha_1_1" name="evento_fecha_1_1"  size="1"  maxlength="2" value="" type="text" readonly="readonly"></span>
					<span><input id="evento_fecha_1_2" name="evento_fecha_1_2"  size="1" maxlength="2" value="" type="text" readonly="readonly">
					<label for="evento_fecha_1_2" ></label></span>
					<span><input id="evento_fecha_1_3" name="evento_fecha_1_3" size="3"  maxlength="4" value="" type="text" readonly="readonly">
					<label for="evento_fecha_1_3" ></label></span>
					
					<span id="calendar_2"><img id="cal_img_2" class="datepicker" src="images/calendar.gif" alt="Elige una fecha." ></span>
					<script type="text/javascript">
						Calendar.setup({
						inputField	 : "evento_fecha_1_3",
						baseField    	 : "evento_fecha_1",
						displayArea    : "calendar_2",
						button		 : "cal_img_2",
						ifFormat	 : "%d/%m/%Y",  
						onSelect	 : selectEuropeDate
						});
					</script>
		</td></tr>
		<tr><td align="right" style="font-size:12px;font-weight:bold;"><label>Hora Inicio:</label></td>
		<td> &nbsp;<select name="hora_evento" id="hora_evento">
			<? for ($ii=0 ; $ii<24; $ii++) { ?>
			<option value="<?=$ii?>"><?if($ii<10) { echo "0"; } echo $ii; ?></option>
			<?}?>
			</select>:<select name="minuto_evento" id="minuto_evento">
			<? for ($ij=0 ; $ij<60; $ij = $ij+5) { ?>
			<option value="<?=$ij?>"><?if($ij<10) { echo "0"; } echo $ij; ?></option>
			<?}?>
			</select>
			<label>Hora Fin: &nbsp;</label><select name="hora_evento_fin" id="hora_evento_fin">
			<? for ($ji=0 ; $ji<24; $ji++) { ?>
			<option value="<?=$ji?>"><?if($ji<10) { echo "0"; } echo $ji; ?></option>
			<?}?>
			</select>:<select name="minuto_evento_fin" id="minuto_evento_fin">
			<? for ($jj=0 ; $jj<60; $jj = $jj+5) { ?>
			<option value="<?=$jj?>"><?if($jj<10) { echo "0"; } echo $jj; ?></option>
			<?}?>
			</select>
		</td></tr>
		<tr><td align="right" style="font-size:12px;font-weight:bold;"><label>Titulo: (*)</label> </td><td colspan="3">
		<input type="text" name="txtTituloEvento" id="txtTituloEvento" value="" class="login" maxlength="80" style="width: 242px;" /></td></tr>
		<tr><td align="right" style="font-size:12px;font-weight:bold;"><label>Ubicaci&oacute;n: </label> </td><td colspan="3">
		<input type="text" name="txtUbicacion" id="txtUbicacion" value="" class="login" maxlength="80" style="width: 242px;" /></td></tr>
		<tr><td align="right" style="font-size:12px;font-weight:bold;"><label>Descripcion: </label></td>
		<td colspan="3"> <textarea type="text" name="txtDescEvento" id="txtDescEvento" value="" class="login" maxlength="255" style="width: 242px; height: 90px;" ></textarea>
		</td></tr>
		<tr><td colspan="2" align="center">
		<br/><input type="submit" class="boton" value="Aceptar">
		</td></tr>
		</table>
		</form>
		</div>	
	</div>
<? 
include "common/inc_database_close.php";
include "footer.php"; ?>