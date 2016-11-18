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
$RequireAccess = "A|L|P|Y";

include "access.php";
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
$bHayError = FALSE;

$strPathFisico = realpath("./" . $DIR_ENTREGAS) . "\\";
$strReturnUrl = "mistps.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];

//Valido que sea un usuario correcto
$iIdComision = "0";
if(GetParameter("action") == "new" && GetParameter("idt") != "" && is_numeric(decrypt(GetParameter("idt")))){
	include "common/inc_database_open.php";
	$strSql = "SELECT UC.comision_id FROM trabajo_practico AS TP " . 
			  " INNER JOIN usuario_comision UC ON UC.comision_id = TP.comision_id " .
			  " WHERE TP.activo = 'Y' AND UC.activo = 'Y' AND TP.tp_id = " . SafeSql(decrypt(GetParameter("idt"))) . " AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]);
	
	$Result = mysql_query($strSql);
	if($oRs = mysql_fetch_array($Result))
	{
		$iIdComision = $oRs["comision_id"];
	}else{
		header("Location:nopermissions.php");
		exit();
		mysql_free_result($Result);
		include "common/inc_database_close.php";
	}
	mysql_free_result($Result);
	include "common/inc_database_close.php";
}
//FIN:Valido que sea un usuario correcto
	
	if ( GetParameter("btnActualizar") != "" ) 
	{
		include "common/inc_database_open.php";
		
		$iIdTP = "";
		if(GetParameter("idt") != "" && is_numeric(decrypt(GetParameter("idt")))){
			$iIdTP = decrypt(GetParameter("idt"));
		}
		
		// Upload de Archivo	
		$strArchivo = "";
		$new_file = $_FILES["txtArchivo"];
		if ($new_file['name'] != "") {
			$file_tmp = $new_file['tmp_name'];
			$file_size = $new_file['size'];
			$file_type = $new_file['type'];
			if ($file_size <= $SIZE_MAX) {
				if($file_type == "application/msword"){
					$strArchivo = date('d',time()) . date('m',time()) . date('Y',time()) . date('H',time()) . date('i',time()) . date('s',time()) . "_" . str_replace(" ", "_", $new_file['name']);
					
					
					if (move_uploaded_file($file_tmp, $strPathFisico . $strArchivo)){
						$bSuccess = true;
						?>
						<script type="text/javascript" src="js/ajaxdoc.js"></script>
						<script language="javascript" type="text/javascript">
							ConvertirDoc("<?=$strArchivo?>");
						</script>
						<?
					}else{
						$MsgBoxMessage = "No se pudo cargar el archivo, por favor verifique que el tamaño sea menor a 2MB e inténtelo nuevamente.";
					}
				}else{
					$MsgBoxMessage = "El archivo debe ser un documento MS Word 97-2003 (*.doc).";
				}
			}else{
				$MsgBoxMessage = "El archivo debe pesar menos de 2MB.";
			}
		}

		// Es un nuevo registro
		if ( GetParameter("action") == "new" && $MsgBoxMessage == "" && $iIdTP != "" )
		{
			$iVersion = 1;
			// Obtengo la versión.
			$Result = mysql_query("SELECT MAX(entrega_version) AS version FROM entrega WHERE activo = 'Y' AND tp_id = " . SafeSql($iIdTP) . " AND grupo_id = " . SafeSql(GetParameter("cmbGrupo")));
			if($oRs = mysql_fetch_array($Result))
			{
				$iVersion = $oRs["version"] + 1;
			}
			mysql_free_result($Result);
			
			if($MsgBoxMessage == ''){
				// Armo la sentencia de INSERT
				$strSQLComm = " INSERT INTO entrega(tp_id, grupo_id, entrega_version, entrega_fechacreacion, entrega_fechaentrega, entrega_observacion, entrega_archivo, entrega_estado, activo " .
								" ) " .
								" VALUES (" .
								SafeSql($iIdTP) . "," .
								SafeSql(GetParameter("cmbGrupo")) . "," .
								SafeSql($iVersion) . "," .
								"NOW()," .
								"NOW()," .
								"'" . SafeSql(GetParameter("txtDesarrollo")) . "'," .
								"'" . SafeSql($strArchivo) . "'," .
								"'E'," . 
								"'Y'" .
								")";
				mysql_query($strSQLComm);
			
				// Obtengo si ocurrió algún error
				if(mysql_error() != ""){
					//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
					$MsgBoxMessage = "No se pudo cargar la entrega, por favor inténtelo nuevamente.";
				}else{
					$iIdComision = "0";
					$iId = "0";
					
					$Result = mysql_query("SELECT comision_id AS Id FROM trabajo_practico WHERE tp_id = " . SafeSql($iIdTP));
					if ( $ObjRs = mysql_fetch_array($Result) )
					{
						$iIdComision = $ObjRs["Id"];
					}
					/* Liberar conjunto de resultados */
					mysql_free_result($Result);
					
					$Result = mysql_query("SELECT MAX(entrega_id) AS Id FROM entrega");
					if ( $ObjRs = mysql_fetch_array($Result) )
					{
						$iId = $ObjRs["Id"];
					}
					/* Liberar conjunto de resultados */
					mysql_free_result($Result);
					
					$strNombreMensaje = "VirtualU";
					$strMail = "";
					$strTituloMensaje = "Nueva Entrega a TP";
					$strMensaje = "Se ha cargado una nueva entrega a un TP. Para visualizarlo ingrese <a href=" . $SITE_URL . "/corregirtp.php?id=" . encrypt($iId) . ">aquí</a>.<br>Si no puede ingresar al link, copie y pegue en su navegador la siguiente url: <b>". $SITE_URL . "/corregirtp.php?id=" . encrypt($iId) . "</b>";
					$strMail = ObtenerMails($iIdComision, "C", "'P','Y'");
					
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
			$strSQLComm = " UPDATE entrega SET " .
						  	" entrega_fechaentrega=NOW(),";
			if($strArchivo != ""){
				//Cargo el nuevo archivo a la base
				$strSQLComm = $strSQLComm .	" entrega_archivo='" . SafeSql($strArchivo) . "',";
				
				// Borro el archivo viejo.
				$Result = mysql_query("SELECT entrega_archivo FROM entrega WHERE entrega_archivo <> '' AND entrega_id = " . decrypt(GetParameter("id")));
				if($oRs = mysql_fetch_array($Result))
				{
					BorrarArchivo($strPathFisico . $oRs["entrega_archivo"]);
				}
				mysql_free_result($Result);
			}
			$strSQLComm = $strSQLComm .	" entrega_observacion='" . SafeSql(GetParameter("txtDesarrollo")) . "'" .
							" WHERE entrega_id = " . SafeSql(decrypt(GetParameter("id")));
			//echo $strSQLComm;exit();
			mysql_query($strSQLComm);
			
			// Obtengo si ocurrió algún error
			$strMsqlError = mysql_error();
			

			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $strMsqlError == "" )
			{
				?><script language="javascript" type="text/javascript">self.location='<?=$strReturnUrl?>';</script><?
				exit();
			}
			else
			{
				$MsgBoxMessage = "No se pudo modificar la entrega, por favor inténtelo nuevamente.";
				$bHayError = TRUE;
			}
		}
	}else{
		// Borro el registro
		if ( GetParameter("btnBorrar") != "" && GetParameter("id") && is_numeric(decrypt(GetParameter("id"))) )
		{
			/*include "common/inc_database_open.php";
			
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
			*/
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
			$BoxTitle = "Modificar entrega";
			$iId = decrypt(GetParameter("id"));
			
			$strGrupo = "";
			$strDesarrollo = "";
			$strArchivo = "";	
			$strCreado = "";
			$strFechaEntrega = "";
				
			
			// Obtengo la entrega.
			$strSQLCommEntregas = " SELECT DISTINCT TP.tp_titulo, E.tp_id, E.grupo_id, E.entrega_version, E.entrega_archivo, E.entrega_observacion, E.entrega_fechacreacion, " .
							" E.entrega_fechaentrega, E.entrega_estado, E.activo, G.grupo_nombre, E.entrega_id " . 
							" FROM entrega AS E " . 
							" INNER JOIN trabajo_practico AS TP ON (E.tp_id = TP.tp_id AND TP.activo = 'Y') " . 
							" INNER JOIN usuario_grupo AS UG ON (UG.grupo_id = E.grupo_id AND UG.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
							" INNER JOIN grupo AS G ON (G.grupo_id = UG.grupo_id AND G.activo = 'Y') " . 
							" INNER JOIN usuario_comision AS UC ON (UC.comision_id = G.comision_id AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
							" WHERE UC.activo = 'Y' " .
							" AND E.activo = 'Y' " .
							" AND E.entrega_estado = 'E' " .
							" AND E.entrega_id = " . SafeSql($iId);
			
			$Result = mysql_query($strSQLCommEntregas);
			
			if ( $oRs = mysql_fetch_array($Result) )
			{
				$iId = $oRs["entrega_id"];
				$strGrupo = $oRs["grupo_nombre"];
				$strCreado = DateTimeFormat($oRs["entrega_fechacreacion"]);
				$strFechaEntrega = DateTimeFormat($oRs["entrega_fechaentrega"]);
				$strDesarrollo = $oRs["entrega_observacion"];
				$strArchivo = $oRs["entrega_archivo"];	
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
			$BoxTitle = "Nueva entrega";
			$strGrupo = "";
			$strCreado = "";
			$strFechaEntrega = "";
			$strDesarrollo = GetParameter("txtDesarrollo");
			$strArchivo = "";
			$iIdGrupo = GetParameter("cmbGrupo");
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
		if(thisForm.cmbGrupo.value == ''){
			alert('Debe seleccionar un grupo.');
			thisForm.cmbGrupo.focus();
			return false;
		}
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
	<input type="hidden" name="idt" value="<?=GetParameter("idt")?>">
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
				<td align="right">Creado:</td>
				<td><?=$strCreado?></td>
			</tr>
			<tr>
				<td align="right">Entregado:</td>
				<td><?=$strFechaEntrega?></td>
			</tr>
			<tr>
				<td align="right">Grupo:</td>
				<td><?=$strGrupo?></td>
			</tr>
			<?
		}else{?>
			<tr>
				<td align="right" valign="top">Grupo:</td>
				<td>
					<? ObtenerComboGrupo($_SESSION["UserId"], $iIdGrupo, $iIdComision, "", "--Seleccione--")?><br />
					<small>* Si no aparece un grupo para seleccionar, debe primero asociarse a uno para la comisi&oacute;n correspondiente.</small>
				</td>
			</tr>
		<?
		}?>
		<tr>
			<td valign="top" align="right">Observaci&oacute;n:</td>
			<td><textarea name="txtDesarrollo" rows="15" cols="50"><?=$strDesarrollo?></textarea></td>
		</tr>
		<tr>
			<td align="right">Archivo:</td>
			<td>
				<input class="inputTxt" type="file" name="txtArchivo" value="" /> <small>* Documento MS Word 97-2003 (*.doc) y tamaño máximo 2MB</small>
				<? if($strArchivo != ""){?>
					<a href="descarga.php?id=<?=encrypt($iId)?>&t=E"><img src="images/download.png" title="Descargar" alt="Descargar" style="border:0; width:38px;"/></a>
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
						<input class="boton" type="submit" name="btnActualizar" value="Actualizar">
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