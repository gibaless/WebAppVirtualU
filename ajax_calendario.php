<?
session_start();
	include "common/inc_database_open.php";
	include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
	include "common/func_getparameter.inc";	
	$action = GetParameter("action");
	
	switch($action) {
	
	case 'startCalendar':
		$month = GetParameter("month");
		$year = GetParameter("year");
		
		if(($month == 0) || ($year == 0)) {
			$thisDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		} else {
			$thisDate = mktime(0, 0, 0, $month, 1, $year);
		}

		echo '<div>
				<form name="changeCalendarDate">
					<select id="ccMonth" onChange="startCalendar($F(\'ccMonth\'), $F(\'ccYear\'))">';
						
						for($i=0; $i<=11; $i++)
						{
							$monthNumber = ($i+1);
							$monthMaker = mktime(0, 0, 0, $monthNumber, 1, 2010);
							if($month > 0) {
								if($month == $monthNumber) {
									$sel = 'selected';
								} else {
									$sel = '';
								}
							} else {
								if(date("m", $thisDate) == $monthNumber) {
									$sel = 'selected';
								} else {
									$sel = '';
								}
							}
							
						$monthName = array('Enero','Febrero','Marzo','Abril', 
						'Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
						echo '<option value="'. $monthNumber .'" '. $sel .'>'. $monthName[$i] .'</option>';
						}
						
				echo '</select>
						&nbsp;
						<select id="ccYear" onChange="startCalendar($F(\'ccMonth\'), $F(\'ccYear\'))">';
						
						$yStart = 2011;
						$yEnd = ($yStart + 10);
						for($i=$yStart; $i<$yEnd; $i++)
						{
							if($year > 0) {
								if($year == $i) {
									$sel = 'selected';
								} else {
									$sel = '';
								}
							} else {
								if(date("Y", $thisDate) == $i) {
									$sel = 'selected';
								} else {
									$sel = '';
								}
							}
							echo '<option value="'. $i .'" '. $sel .'>'. $i .'</option>';
						}
						
				echo '</select>';
				
				//Control de Permisos
				if($_SESSION["UserAccess"] == "P" || $_SESSION["UserAccess"] == "A" || $_SESSION["UserAccess"] == "Y"){
				?>
					&nbsp;&nbsp;<a href="#" class="boton" onClick="showEventForm();" style="font-size: small;">Crear Nuevo Evento</a>
					
				<?
				}else{
					// Para los alumnos no se puede crear un nuevo evento.
				} 
				
				echo '</form>
				</div>';
		echo '<div style="line-height:10px;">&nbsp;</div>';
		// Muestra los dias de la semana
		echo '<div class="calendarFloat" style="text-align: center; background: #ffec8b url(images/dias.png) repeat-x; height: 38px;"><span style="position: relative; top: 10px;"><b>Lun</b></span></div>
				<div class="calendarFloat" style="text-align: center;  background: #ffec8b url(images/dias.png) repeat-x;  height: 38px;"><span style="position: relative; top: 10px;"><b>Mar</b></span></div>
				<div class="calendarFloat" style="text-align: center;  background: #ffec8b url(images/dias.png) repeat-x;  height: 38px;"><span style="position: relative; top: 10px;"><b>Mie</b></span></div>
				<div class="calendarFloat" style="text-align: center;  background: #ffec8b url(images/dias.png) repeat-x;  height: 38px;"><span style="position: relative; top: 10px;"><b>Jue</b></span></div>
				<div class="calendarFloat" style="text-align: center;  background: #ffec8b url(images/dias.png) repeat-x;  height: 38px;"><span style="position: relative; top: 10px;"><b>Vie</b></span></div>
				<div class="calendarFloat" style="text-align: center;  background: #ffec8b url(images/dias.png) repeat-x;  height: 38px;"><span style="position: relative; top: 10px;"><b>Sab</b></span></div>
				<div class="calendarFloat" style="text-align: center;  background: #ffec8b url(images/dias.png) repeat-x;  height: 38px;"><span style="position: relative; top: 10px;"><b>Dom</b></span></div>';
				
		// Muestra el calendario un for que cuenta hasta la cantidad de dias del mes actual para ese año.
		for($i=0; $i<date("t", $thisDate); $i++)
		{
			$thisDay = ($i + 1);
			if(($month == 0) || ($year == 0)) {
				$finalDate = mktime(0, 0, 0, date("m"), $thisDay, date("Y"));
				$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
				$fdf = mktime(0, 0, 0, date("m"), 1, date("Y"));
				$month = date("m");
				$year = date("Y");
			} else {
				$finalDate = mktime(0, 0, 0, $month, $thisDay, $year);
				$fdf = mktime(0, 0, 0, $month, 1, $year);
			
			}
			$fechaeventofinal = date("Y-m-d",$finalDate);
			
			// Skip some cells to take into account for the weekdays.
			if($i == 0) {
				$firstDay = date("w", $fdf);
				$skip = ($firstDay - 1);
				if($skip < 0) { $skip = 6; }
				
				for($s=0; $s<$skip; $s++)
				{
					echo '<div class="calendarFloat" style="border: 1px solid #C6DEFF;">&nbsp;</div>';
				}
			}
							
			// Si quiero destacar sabados y domingos
			/* if((date("w", $finalDate) == 0) || (date("w", $finalDate) == 6)) {	$bgColor = '#D0E6FF';} else { $bgColor = '#ECF4FF';}
			*/

			// Cuando hago click sobre el dia siempre me muestra los eventos que hay ese dia: para todos los perfiles!
				$onClick = 'displayEvents('. $thisDay .', '. $month .', '. $year .')';
				
			// Display the day.

			echo '<div class="calendarFloat" id="calendarDay_'. $thisDay .'" style="background: url(images/bg_day.png) no-repeat; cursor: pointer;" 
									onMouseOver="highlightCalendarCell(\'calendarDay_'. $thisDay .'\')"
									onMouseOut="resetCalendarCell(\'calendarDay_'. $thisDay .'\')"
									onClick="'. $onClick .'">';
									?>
									
						<span style="position: relative; left: 1px;top: 2px;">&nbsp;<?=$thisDay?> 
						
			<?
			//Tengo que chequear la cantidad de eventos para la persona que lo está viendo.
			if($_SESSION["UserAccess"] == "A"){  
			$strSQLCant_Eventos = "SELECT COUNT(evento_id) as cantidad_eventos FROM event WHERE evento_fecha = '". $fechaeventofinal . "' LIMIT 1" ; }
			if($_SESSION["UserAccess"] == "P" || $_SESSION["UserAccess"] == "Y" || $_SESSION["UserAccess"] == "L"){
			//Listo todos los eventos de las comisiones a las que estoy registrado.
			 $strSQLCant_Eventos = " SELECT COUNT(DISTINCT E.evento_id) as cantidad_eventos FROM event AS E " . 
					" INNER JOIN usuario_comision AS UC ON E.comision_id = UC.comision_id " . 
					" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " .
					" WHERE UC.activo = 'Y'  AND UC.usuario_id = '" . SafeSql($_SESSION["UserId"]) . "' " . 
					" AND E.evento_fecha = '$fechaeventofinal' " ;
			}			
			
			$Query_chequeasihayeventos = mysql_query($strSQLCant_Eventos);
			if($Query_chequeasihayeventos) {
				if(mysql_num_rows($Query_chequeasihayeventos) > 0) {
					$cant_eventos_del_dia = mysql_fetch_array($Query_chequeasihayeventos);
					if($cant_eventos_del_dia['cantidad_eventos'] != 0){
						echo "<br/> &nbsp;&nbsp;&nbsp;&nbsp;(<b>" . $cant_eventos_del_dia['cantidad_eventos'] . "</b>)";
					}
				} 
			} ?>	
				</span>	
					</div>
		<?
		}
		
		break;
	
	case 'eventList':
		$day = GetParameter("d");
		if ($day < 10){ $day = '0' . $day;}
		$month = GetParameter("m");
		$year = GetParameter("y");

		$datedeEventos = $year . '-' . $month . '-' . $day;
		//Query para listar eventos.
		//Si soy administrador voy a traer todos los eventos de todas las comisiones/...de todas las universidades...
		//Si soy profesor solamente voy a traer los eventos que son de las comisiones de las que yo soy profesor (esas voy a poder editar)
		//Y si soy alumno puedo ver los eventos creados por profesores que pertenezcan a las comisiones a las que pertenezco y que esten activas.
		// 
		if($_SESSION["UserAccess"] == "A")
			
				$strSQLComm = " SELECT distinct E.*, C.comision_titulo as comision, M.materia_titulo as materia, UN.universidad_titulo as universidad, " .
				 " F.facultad_titulo as facultad, R.carrera_titulo as carrera, U.usuario_nombre, U.usuario_apellido " .
				 " FROM event as E " .
				" INNER JOIN usuario AS U ON U.usuario_id = E.usuario_id " . 
				" INNER JOIN usuario_comision AS UC ON E.comision_id = UC.comision_id " . 
				" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " .
				" INNER JOIN materia AS M ON (M.materia_id = C.materia_id AND M.activo = 'Y') " .
				" INNER JOIN carrera AS R ON (R.carrera_id = M.carrera_id AND R.activo = 'Y') " .
				" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " .
				" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y')  " .
				" WHERE E.evento_fecha = '$datedeEventos' " ;
		if($_SESSION["UserAccess"] == "Y" || $_SESSION["UserAccess"] == "P" || $_SESSION["UserAccess"] == "L")
		$strSQLComm = " SELECT distinct E.*, U.usuario_nombre, U.usuario_apellido, C.comision_titulo as comision, M.materia_titulo as materia, ".
					" UN.universidad_titulo as universidad, R.carrera_titulo as carrera, F.facultad_titulo as facultad FROM event AS E " . 
					" INNER JOIN usuario_comision AS UC ON E.comision_id = UC.comision_id " . 
					" INNER JOIN usuario AS U ON U.usuario_id = E.usuario_id " . 
					" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " .
					" INNER JOIN materia AS M ON (M.materia_id = C.materia_id AND M.activo = 'Y') " .
					" INNER JOIN carrera AS R ON (R.carrera_id = M.carrera_id AND R.activo = 'Y') " .
					" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " .
					" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y')  " .
					" WHERE UC.activo = 'Y'  AND UC.usuario_id = '" . SafeSql($_SESSION["UserId"]) . "' " . 
					" AND C.comision_fechahasta >= CURDATE() " .
					" AND E.evento_fecha = '$datedeEventos' " ;
	
		$eventQuery = mysql_query($strSQLComm);
		if($eventQuery) {
		
			if($_SESSION["UserAccess"] == "P" || $_SESSION["UserAccess"] == "A" || $_SESSION["UserAccess"] == "Y"){
				
			?>
			<br/><span  style="text-align:right; margin-left:340px; "><a href="#" class="boton" onClick="showEventForm( <?=$day?>,<?=$month?>, <?=$year?> )">Crear Nuevo Evento en este d&iacute;a</a></span>
			<? }else{ ?>
				<span style="text-align: right; position: relative; margin-left: 340px; top: -20px cursor: pointer;"><br/><br/>&nbsp;</span>

			<? } ?>
			<div style="position: absolute; margin-left: 10px; margin-top: -20px;font-size: 14px; font-family: Verdana, Arial;"><b><?=$day?>/<?=$month?>/<?=$year?></b></div>
				<?
				if(mysql_num_rows($eventQuery) > 0) {

				for($i=0; $i<mysql_num_rows($eventQuery); $i++) {
					
					if($i % 2) { $textColor = '#e6e6fa'; } else { $textColor='#C6E4EE'; }
					
					$msg = mysql_fetch_array($eventQuery);
					
						
						?>
						<div style="margin-top:10px; margin-left:20px;" id="event_<?=$msg['evento_id']?>">
							<b><?=$msg['universidad']?><br/><?=$msg['facultad']?></b> >> <b><?=$msg['carrera']?></b> >> 
							<b><?=$msg['materia']?></b>
						<hr width="540px" align="left" />						
						<div style="font-size: 14px; color: #2A52BE;">&nbsp;&nbsp;<b><?=$msg['evento_titulo']?></b> 
						
						<? if($_SESSION["UserAccess"] == "P" || $_SESSION["UserAccess"] == "A" || $_SESSION["UserAccess"] == "Y"){	 ?>
						<span style="color: blue; text-decoration: underline; cursor: pointer;font-size: 9px;" onClick="deleteEvent(<?=$msg['evento_id']?>)">
						<img alt="Borrar" title="Borrar" src="images/borrar.png" style="width:16px;height:16px;"/>	</span>
						<?}?></div>
						
						<div style="margin-left: 20px; margin-bottom: 6px;">
						<? //Si la hora es todo nula no se muestra. 
						
						if($msg['evento_horafin'] == $msg['evento_horainicio'] && ( $msg['evento_horafin'] == "00:00:00" || $msg['evento_horafin'] == NULL ) ){

							//No muestro la hora

						}else{
			
							$piecesdesde = explode(":", $msg['evento_horainicio']);
							$pieceshasta = explode(":", $msg['evento_horafin']);

							if($msg['evento_horainicio'] != "00:00:00" || $msg['evento_horainicio'] != NULL) echo "&nbsp;Inicia a las " . $piecesdesde[0]. ":". $piecesdesde[1] . "hs";
							if($msg['evento_horafin'] != "00:00:00")	echo " y finaliza a las " . $pieceshasta[0]. ":". $pieceshasta[1]  . "hs.";

						}
						
						?>
						<? if($msg['evento_ubicacion']!=''){ ?><br/>&nbsp;<b>Ubicaci&oacute;n:</b> <?=$msg['evento_ubicacion']?>  <? } ?>
						<? if($msg['evento_descripcion']!=''){ ?><br/>&nbsp;<b>Descripci&oacute;n:</b> <?=$msg['evento_descripcion']?>  <? } ?>
						</div>
						
						</div>
						<?
							
						} // for.
				
				?>
			</div>
			<?
			} else {
				?> <p style="margin-left: 20px; margin-top: 50px; color: black;" align="left">No hay eventos agendados para este d&iacute;a. </p>
				<?
				}
				  ?>
				</div>
		
		<? } else { ?>
			<p style=" margin-left: 30px;  margin-top: 65px;  color: gray;" align="left">Error al obtener los resultados. Intenta nuevamente. </p>
		<? }
		
		break;
	
	case 'addEvent':

		$comision = GetParameter("com");
		$day = GetParameter("d");
		$month = GetParameter("m");
		$year = GetParameter("y");
		if($day < 10){ $day = '0'. GetParameter("d"); }
		if($month < 10){ $month = '0'. GetParameter("m"); }

		$evento_fecha = $year.'-'.$month.'-'.$day;
		$idCreadordelEvento = $_SESSION["UserId"];
		$titulo = GetParameter("t");
		$ubicacion = GetParameter("ub");
		$descripcion = GetParameter("desc");
		$min = '';
		$hora = '';
		if(GetParameter("hora") < 10) { $hora = '0';	}
		$hora = $hora . GetParameter("hora");
		
		if( GetParameter("min") < 10) { $min = '0'; }
		$min = $min . GetParameter("min"); 
		
		$hora_inicio = $hora.':'.$min.':00';
		//Hora y Fecha Hasta
		$minfin = '';
		$horafin = '';
		if(GetParameter("horafin") < 10)  { $horafin = '0';}
		$horafin = $horafin . GetParameter("horafin");
	
		if( GetParameter("minfin") < 10) 	{ $minfin = '0'; }
		$minfin = $minfin . GetParameter("minfin"); 
		
		$hora_fin = $horafin.':'.$minfin.':00';

		$eventoDescripcion = addslashes(trim($descripcion));
		$strSQLComm4 = "INSERT INTO event (evento_titulo, evento_descripcion, evento_ubicacion, evento_fecha, evento_horainicio, evento_horafin, usuario_id, comision_id) " .
               " VALUES ('" . $titulo . "', '" . $eventoDescripcion . "', '" . $ubicacion . "','" . $evento_fecha . "', '" . $hora_inicio . "','" . $hora_fin . "'," . $idCreadordelEvento . "," . $comision . " )";


		$addEvent = mysql_query($strSQLComm4);
		
		break;
	
	case 'deleteEvent':
		if($_SESSION["UserAccess"] == "P" || $_SESSION["UserAccess"] == "A" || $_SESSION["UserAccess"] == "Y"){
			$eid = GetParameter("eid");
			if(is_numeric($eid)) {
				$deleteIt = mysql_query("DELETE FROM event WHERE evento_id='$eid' LIMIT 1");
			} else {
				// Dont do anything.
			}
		} else {
			// Dont delete it.
		}
		break;
		
		
	default:
		echo 'Error';
		break;
	}

?>