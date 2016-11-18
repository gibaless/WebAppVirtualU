<?
session_start();

/* INCLUYO ARCHIVO DE CONFIGURACIÓN. */
include "includes/sitesettings_inc.php";
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.

header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
//header("ExpiresAbsolute: " . time() -10); //Expira página Absolutamente
header("Expires: -100000"); //Expira página

$strMsqlError = "";
$MsgBoxMessage = "";
$MsgBoxMessageError = "";
$BoxWidth = "";
$BoxEdit = "Off";
$BoxHelp = "Off";
$current0 = "On";

$strFile = "";

// Defino variable de acceso para el INCLUDE: Access
$RequireAccess = "A|P|Y";

include "access.php";
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
$bHayError = FALSE;

$strPathFisico = realpath("./" . $DIR_TPS) . "\\";
$strReturnUrl = "mistps.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];
	
	if ( GetParameter("btnActualizar") != "" ) 
	{
		include "common/inc_database_open.php";
		
		$iIdComision= "";
		if(GetParameter("cmbComision") != "" && is_numeric(GetParameter("cmbComision"))){
			$iIdComision = GetParameter("cmbComision");
		}
		
		$strFechaEntrega = "NULL";
		if(GetParameter("fecha_1_1") != ""){
			$strFechaEntrega = SqlDate(GetParameter('fecha_1_1') ."/". GetParameter('fecha_1_2') . "/". GetParameter('fecha_1_3'));
		}
		
		// Upload de Archivo	
		$strArchivo = "";
		$new_file = $_FILES["txtArchivo"];
		if ($new_file['name'] != "") {
			if ($new_file['size'] <= $SIZE_MAX) {
				$strArchivo = date('d',time()) . date('m',time()) . date('Y',time()) . date('H',time()) . date('i',time()) . date('s',time()) . "_" . str_replace(" ", "_", $new_file['name']);
				$file_tmp = $new_file['tmp_name'];
				$file_size = $new_file['size'];
				$file_type = $new_file['type'];
				
				if (move_uploaded_file($file_tmp, $strPathFisico . $strArchivo)){
					$bSuccess = true;
				}else{
					$MsgBoxMessage = "No se pudo cargar el archivo, por favor verifique que el tamaño sea menor a 2MB e inténtelo nuevamente.";
				}
			}else{
				$MsgBoxMessage = "El archivo debe pesar menos de 2MB.";
			}
		}

		// Es un nuevo registro
		if ( GetParameter("action") == "new" && $MsgBoxMessage == "" && $iIdComision != "" )
		{
			
			if($MsgBoxMessage == ''){
				// Armo la sentencia de INSERT
				$strSQLComm = " INSERT INTO trabajo_practico(comision_id, tp_titulo, tp_fechaalta, tp_fechaentrega, tp_descripcion, tp_archivo, usuario_id, activo " .
								" ) " .
								" VALUES (" .
								SafeSql($iIdComision) . "," .
								"'" . SafeSql(GetParameter("txtTitulo")) . "'," .
								"NOW()," .
								$strFechaEntrega . "," .
								"'" . SafeSql(GetParameter("txtDesarrollo")) . "'," .
								"'" . SafeSql($strArchivo) . "'," .
								SafeSql($_SESSION["UserId"]) . "," . 
								"'Y'" .
								")";
				mysql_query($strSQLComm);
			
				// Obtengo si ocurrió algún error
				if(mysql_error() != ""){
					//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
					$MsgBoxMessage = "No se pudo cargar el trabajo práctico, por favor inténtelo nuevamente.";
				}else{
					$iId = "";
					$Result = mysql_query("SELECT MAX(tp_id) AS Id FROM trabajo_practico");
					if ( $ObjRs = mysql_fetch_array($Result) )
					{
						$iId = $ObjRs["Id"];
					}
					/* Liberar conjunto de resultados */
					mysql_free_result($Result);
				
					$strNombreMensaje = "VirtualU";
					$strMail = "";
					$strTituloMensaje = "Nuevo TP";
					$strMensaje = "Se ha cargado un nuevo TP. Para visualizarlo ingrese <a href=" . $SITE_URL . "/tpver.php?id=" . encrypt($iId) . ">aquí</a>.<br>Si no puede ingresar al link, copie y pegue en su navegador la siguiente url: <b>". $SITE_URL . "/tpver.php?id=" . encrypt($iId) . "</b>";
					$strMail = ObtenerMails($iIdComision, "C", "'P','Y','L'");
				}
				
			}
			
			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $MsgBoxMessage == "" )
			{
				include "enviarmail.php";
				?><script language="javascript" type="text/javascript">EnviarMail('<?=$strNombreMensaje?>', '<?=$strMail?>', '<?=$strTituloMensaje?>', '<?=$strMensaje?>' );</script>
				<script language="javascript" type="text/javascript">self.location='<?=$strReturnUrl?>';</script><?
				exit();
			}
			else
			{
				$bHayError = TRUE;
			}
		}
		else if ( GetParameter("action") == "edit" && GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id"))) && $MsgBoxMessage == "" )
		{
			
			// ARMO LA SENTENCIA DE UPDATE
			$strSQLComm = " UPDATE trabajo_practico SET " .
						  	" tp_titulo='" . SafeSql(GetParameter("txtTitulo")) . "'," .
							" tp_fechaentrega=" . $strFechaEntrega . ",";
			if($strArchivo != ""){
				//Cargo el nuevo archivo a la base
				$strSQLComm = $strSQLComm .	" tp_archivo='" . SafeSql($strArchivo) . "',";
				
				// Borro el archivo viejo.
				$Result = mysql_query("SELECT tp_archivo FROM trabajo_practico WHERE tp_archivo <> '' AND tp_id = " . decrypt(GetParameter("id")) . " AND usuario_id = " . SafeSql($_SESSION["UserId"]));
				if($oRs = mysql_fetch_array($Result))
				{
					BorrarArchivo($strPathFisico . $oRs["tp_archivo"]);
				}
				mysql_free_result($Result);
			}
			$strSQLComm = $strSQLComm .	" tp_descripcion='" . SafeSql(GetParameter("txtDesarrollo")) . "'" .
							" WHERE tp_id = " . SafeSql(decrypt(GetParameter("id")));
			//echo $strSQLComm;exit();
			mysql_query($strSQLComm);
			
			// Obtengo si ocurrió algún error
			$strMsqlError = mysql_error();
			

			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $strMsqlError == "" )
			{
				header("Location: $strReturnUrl");
				exit();
			}
			else
			{
				$MsgBoxMessage = "No se pudo modificar el trabajo práctico, por favor inténtelo nuevamente.";
				$bHayError = TRUE;
			}
		}
	}else{
		// Borro el registro
		if ( GetParameter("btnBorrar") != "" && GetParameter("id") && is_numeric(decrypt(GetParameter("id"))) )
		{
			include "common/inc_database_open.php";
			
			// Borro el archivo.
			$Result = mysql_query("SELECT tp_archivo FROM trabajo_practico WHERE tp_archivo <> '' AND tp_id = " . decrypt(GetParameter("id")) . " AND usuario_id = " . SafeSql($_SESSION["UserId"]));
			if($oRs = mysql_fetch_array($Result))
			{
				BorrarArchivo($strPathFisico . $oRs["tp_archivo"]);
				
				// Elimino el registro
				$strSQLComm = " DELETE FROM trabajo_practico " .
							  " WHERE tp_id = " . decrypt(GetParameter("id")) . " AND usuario_id = " . SafeSql($_SESSION["UserId"]);
				mysql_query($strSQLComm);
				
				// Obtengo si ocurrió algún error
				$strMsqlError = mysql_error();
			}
			mysql_free_result($Result);
			
			
			
			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $strMsqlError == "" )
			{
				header("Location: $strReturnUrl");
				exit();
			}
			else
			{
				$bHayError = TRUE;
			}
			
		}
	}
	// FIN DE: Si se agrega un nuevo registro, realizo el alta, sino actualizo los datos


include "header.php";
include "common/inc_database_open.php";

		// Seteo variables que se mostrarán en los INPUTS
		//$MsgBoxMessage = "";

		if ( GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id"))) && GetParameter("action") == "edit" ) 
		{
			$BoxTitle = "Modificar trabajo práctico";
			$iId = decrypt(GetParameter("id"));
			
			$strComision = "";
			$strMateria = "";
			$strCarrera = "";
			$strFacultad = "";
			$strUniversidad = "";
			$strFecha = "";
			$strDia = "";
			$strMes = "";
			$strAnio = "";
			$strTitulo = "";
			$strDesarrollo = "";
			$strArchivo = "";	
			$strCreado = "";
				
			
			// Obtengo el tp.
			$strSQLCommTPs = " SELECT DISTINCT TP.tp_id, TP.tp_titulo, TP.tp_descripcion, Tp.tp_fechaalta, TP.tp_fechaentrega, Tp.tp_archivo, UR.usuario_id, UR.usuario_nombre, UR.usuario_apellido, C.comision_fechadesde, C.comision_fechahasta, U.tipo_usuario, UC.usuario_tipo, " . 
							" C.comision_titulo AS comision, C.comision_codigo, M.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, DAY(TP.tp_fechaentrega) AS diaent, MONTH(TP.tp_fechaentrega) AS mesent, YEAR(TP.tp_fechaentrega) AS anioent " .
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
							" AND C.comision_fechadesde <= CURDATE() AND C.comision_fechahasta >= CURDATE() " .
							" AND (UC.usuario_tipo = 'P' OR UC.usuario_tipo = 'Y') " . 
							" AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . 
							" AND TP.tp_id = " . SafeSql($iId);
			
			$Result = mysql_query($strSQLCommTPs);
			
			if ( $oRs = mysql_fetch_array($Result) )
			{
				$iIdTP = $oRs["tp_id"];
				$strComision = $oRs["comision"];
				if($oRs["comision_codigo"] != ""){
					$strComision = $strComision . " (" . $oRs["comision_codigo"] . ")";
				}
				$strMateria = $oRs["materia"];
				$strCarrera = $oRs["carrera"];
				$strFacultad = $oRs["facultad"];
				$strUniversidad = $oRs["universidad"];
				$strUsuario = $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"];
				$strFecha = DateTimeFormat($oRs["tp_fechaalta"]);
				$strDia = $oRs["diaent"];
				$strMes = $oRs["mesent"];
				$strAnio = $oRs["anioent"];
				$strTitulo = $oRs["tp_titulo"];
				$strDesarrollo = $oRs["tp_descripcion"];
				$strArchivo = $oRs["tp_archivo"];	
				$strCreado = DateTimeFormat($oRs["tp_fechaalta"]) . " por <strong>" . $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"] . "</strong>";
									
			}
			else
			{
				$MsgBoxMessage = "Datos erroneos.";
				$MsgBoxMessageError = "Datos erroneos.";
			}
			
			/* Liberar conjunto de resultados */
			mysql_free_result($Result);

		}
		else
		{
			// Es un nuevo registro
			$BoxTitle = "Nuevo Trabajo Práctico";
			$strFecha = "";
			$strDia = "";
			$strMes = "";
			$strAnio = "";
			$strTitulo = "";
			$strDesarrollo = "";
			$strArchivo = "";
			$iIdUniversidad = "0";
			if(GetParameter("idu") != "" && is_numeric(decrypt(GetParameter("idu")))){
				$iIdUniversidad = decrypt(GetParameter("idu"));
			}
			$iIdFacultad = "0";
			if(GetParameter("idf") != "" && is_numeric(decrypt(GetParameter("idf")))){
				$iIdFacultad = decrypt(GetParameter("idf"));
			}
			$iIdCarrera= "0";
			if(GetParameter("idr") != "" && is_numeric(decrypt(GetParameter("idr")))){
				$iIdCarrera = decrypt(GetParameter("idr"));
			}
			$iIdMateria= "0";
			if(GetParameter("idm") != "" && is_numeric(decrypt(GetParameter("idm")))){
				$iIdMateria = decrypt(GetParameter("idm"));
			}
			$iIdComision= "0";
			if(GetParameter("idc") != "" && is_numeric(decrypt(GetParameter("idc")))){
				$iIdComision = decrypt(GetParameter("idc"));
			}
			if(GetParameter("action") == "edit")
			{
				$MsgBoxMessage = "Datos erroneos.";
				$MsgBoxMessageError = "Datos erroneos.";
			}
		}
		// FIN DE: Seteo variables que se mostrarán en los INPUTS
	

?>
<script language="JavaScript" type="text/javascript">
function validarForm(thisForm) {
	
	<? if(GetParameter("action") == "new"){?>
		if(thisForm.cmbComision.value == ''){
			alert('Debe seleccionar la comision.');
			thisForm.cmbComision.focus();
			return false;
		}
	<?
	}?>
	
	if(thisForm.fecha_1_1.value == ''){
		alert('Debe ingresar la fecha de entrega.');
		thisForm.fecha_1_1.focus();
		return false;
	}
	
	if(thisForm.txtTitulo.value == ''){
		alert('Debe ingresar el título.');
		thisForm.txtTitulo.focus();
		return false;
	}

	<? if(GetParameter("action") == "new"){?>
		if(thisForm.txtArchivo.value == ''){
			alert('Debe ingresar el archivo.');
			thisForm.txtArchivo.focus();
			return false;
		}
	<?
	}?>
	
	return true;
}
</script>
<h3><?=$BoxTitle?></h3>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" enctype="multipart/form-data" method="post" name="frmForm" onsubmit="return validarForm(this);">
	<input type="hidden" name="id" value="<?=GetParameter("id")?>">
	<input type="hidden" name="action" value="<?=GetParameter("action")?>">
	<input type="hidden" id="HidFecha" name="HidFecha">
	<input type="hidden" name="return_url" value="<?=GetParameter("return_url")?>" />
	<!-- FORMULARIO DE EDICION -->
	<?
	// Si hay un error muestro mensaje y termino
	if ($MsgBoxMessage != "") 
	{?>
		
		<p style="margin-left:30px; font-size:11px;"><img src="images/errormsg.png" title="Error" style=" vertical-align:middle;"/>&nbsp;&nbsp;&nbsp;		
		<?=$MsgBoxMessage?></p>
	<? }?>
	<table border="0" cellpadding="2" cellspacing="2" align="center" style="font-size:12px;font-weight:bold;">
		<? if(GetParameter("action") == "edit" && GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id")))){?>
			<tr>
				<td align="right">Subido:</td>
				<td><?=$strCreado?></td>
			</tr>
			<tr>
				<td align="right">Universidad:</td>
				<td><?=$strUniversidad?></td>
			</tr>
			<tr>
				<td align="right">Facultad:</td>
				<td><?=$strFacultad?></td>
			</tr>
			<tr>
				<td align="right">Carrera:</td>
				<td><?=$strCarrera?></td>
			</tr>
			<tr>
				<td align="right">Materia:</td>
				<td><?=$strMateria?></td>
			</tr>
			<tr>
				<td align="right">Comisi&oacute;n:</td>
				<td><?=$strComision?></td>
			</tr>
		<?
		}else{?>
			<tr>
				<td align="right">Universidad: (*)</td>
				<td><div id="dvUniversidades"><? ObtenerComboUniversidad($_SESSION["UserId"], $iIdUniversidad, "onchange=\"TraerFacultades('" . $iIdFacultad . "','--Seleccione--','Y');\"", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div></td>
			</tr>
			<tr>
				<td align="right">Facultad: (*)</td>
				<td><div id="dvFacultades"><? ObtenerComboFacultad($_SESSION["UserId"], $iIdFacultad, $iIdUniversidad, "onchange=\"TraerCarreras('" . $iIdCarrera . "','--Seleccione--','Y');\"", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div></td>
			</tr>
			<tr>
				<td align="right">Carrera: (*)</td>
				<td><div id="dvCarreras"><? ObtenerComboCarrera($_SESSION["UserId"], $iIdCarrera, $iIdFacultad, "onchange=\"TraerMaterias('" . $iIdMateria . "','--Seleccione--','Y');\"", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div></td>
			</tr>
			<tr>
				<td align="right">Materia: (*)</td>
				<td><div id="dvMaterias"><? ObtenerComboMateria($_SESSION["UserId"], $iIdMateria, $iIdCarrera, "onchange=\"TraerComisiones('" . $iIdComision . "','--Seleccione--','Y');\"", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div></td>
			</tr>
			<tr>
				<td align="right">Comisi&oacute;n: (*)</td>
				<td><div id="dvComisiones"><? ObtenerComboComision($_SESSION["UserId"], $iIdComision, $iIdMateria, "", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div></td>
			</tr>
		<?
		}?>
		<tr>
        	<td>Fecha de Entrega: (*)</td>
            <td>
					<span><input id="fecha_1_1" class="AcomodaMiniTextBox" name="fecha_1_1" size="1" maxlength="2" value="<?=$strDia?>" type="text" readonly="readonly" /><label for="fecha_1_1"></label></span>
					<span><input id="fecha_1_2" class="AcomodaMiniTextBox" name="fecha_1_2" size="1" maxlength="2" value="<?=$strMes?>" type="text" readonly="readonly" /><label for="fecha_1_2"></label></span>
					<span><input id="fecha_1_3" class="AcomodaMiniTextBox" name="fecha_1_3" size="3" maxlength="4" value="<?=$strAnio?>" type="text" style="width: 40px;" readonly="readonly" /><label for="fecha_1_3"></label></span>
			
			<span id="calendar_2"><img id="cal_img_2" src="images/calendar.gif" alt="Elige una fecha." class="AcomodaCalendario" ></span>
			<script type="text/javascript">
				Calendar.setup({
				inputField	 : "fecha_1_3",
				baseField    : "fecha_1",
				displayArea  : "calendar_2",
				button		 : "cal_img_2",
				ifFormat	 : "%d/%m/%Y",  
				onSelect	 : selectEuropeDate
				});
			</script>
            </td>
        </tr>
		<tr>
			<td align="right">Título: (*)</td>
			<td><input type="text" name="txtTitulo" value="<?=$strTitulo?>" maxlength="255" style="width:254px;" /></td>
		</tr>
		<tr>
			<td valign="top" align="right">Descripción:</td>
			<td><textarea name="txtDesarrollo" rows="10" cols="60"><?=$strDesarrollo?></textarea></td>
		</tr>
		<tr>
			<td align="right">Archivo: (*)</td>
			<td>
				<input class="inputTxt" type="file" name="txtArchivo" value="" /> <small>* Tamaño máximo 2MB</small>
				<? if($strArchivo != ""){?>
					<a href="descarga.php?id=<?=encrypt($iId)?>&t=T"><img src="images/download.png" title="Descargar" alt="Descargar" style="width:36px;border:0;"></a>
				<? }?>
			</td>
		</tr>
		
		<tr class="TableBtns">
			<td valign="bottom" align="center" colspan="2">
				<?  if ( GetParameter("action") == "new" && $MsgBoxMessageError == "" ) 
					{ ?>
						<input class="boton" type="submit" name="btnActualizar" value="Insertar">
				<?  } ?>
				<?  if ( GetParameter("action") == "edit" && $MsgBoxMessageError == "" ) 
					{ ?>
						<input class="boton" type="submit" name="btnActualizar" value="Actualizar"> <input class="boton" type="button" name="btnBorrar" value="Borrar" onclick="ConfirmDelete('archivoform.php?btnBorrar=yes&id=<?=GetParameter("id")?>&return_url=<?=url_encode($strReturnUrl)?>','Desea realmente borrar el archivo?');" class="FormBtn">
				<?  } ?>
				<input class="boton" type="button" value="Volver" onclick="self.location='<?=$strReturnUrl?>';" name="btnVolver">
			</td>
		</tr>
	</table>
	<!-- FIN DE: FORMULARIO DE EDICION -->
</form>
<?
include "common/inc_database_close.php";
include "footer.php";
?>