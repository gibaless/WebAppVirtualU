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

$strPathFisico = realpath("./" . $DIR_ARCHIVOS) . "\\";
$strReturnUrl = "misarchivoscreados.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];
	
	if ( GetParameter("btnActualizar") != "" ) 
	{
		include "common/inc_database_open.php";
		$strTipo = "";
		$iIdComision= "";
		if(GetParameter("cmbComision") != "" && is_numeric(decrypt(GetParameter("cmbComision")))){
			$iIdComision = decrypt(GetParameter("cmbComision"));
			$strTipo = "C";
		}
		$iIdGrupo= "";
		if(GetParameter("cmbGrupo") != "" && is_numeric(GetParameter("cmbGrupo"))){
			$iIdGrupo = GetParameter("cmbGrupo");
			$strTipo = "G";
		}
		
		
		// Es un nuevo registro
		if ( GetParameter("action") == "new" && $MsgBoxMessage == "" && $strTipo != "" )
		{
			// Upload de Archivo	
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
		
			if($MsgBoxMessage == ''){
				// Armo la sentencia de INSERT
				$strSQLComm = " INSERT INTO archivo(archivo_titulo, archivo_fechaalta, archivo_tipo, archivo_descripcion, archivo_archivo, usuario_id, activo " .
								" ) " .
								" VALUES (" .
								"'" . SafeSql(GetParameter("txtTitulo")) . "'," .
								"NOW()," .
								"'" . $strTipo . "'," .
								"'" . SafeSql(GetParameter("txtDesarrollo")) . "'," .
								"'" . SafeSql($strArchivo) . "'," .
								SafeSql($_SESSION["UserId"]) . "," . 
								"'Y'" .
								")";
				mysql_query($strSQLComm);
			
				// Obtengo si ocurrió algún error
				if(mysql_error() != ""){
					//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
					$MsgBoxMessage = "No se pudo cargar el archivo, por favor inténtelo nuevamente.";
				}else{
					$iId = "";
					$Result = mysql_query("SELECT MAX(A.archivo_id) AS Id FROM archivo AS A");
					if ( $ObjRs = mysql_fetch_array($Result) )
					{
						$iId = $ObjRs["Id"];
					}
					/* Liberar conjunto de resultados */
					mysql_free_result($Result);
					
					$strNombreMensaje = "VirtualU";
					$strMail = "";
					$strTituloMensaje = "Nuevo Archivo";
					$strMensaje = "Se ha subido un nuevo archivo. Para visualizarlo ingrese <a href=" . $SITE_URL . "/archivover.php?id=" . encrypt($iId) . ">aquí</a><br>Si no puede ingresar al link, copie y pegue en su navegador la siguiente url: <b>". $SITE_URL . "/archivover.php?id=" . encrypt($iId) . "</b>";
					
					switch($strTipo){
						case "G":
							$strSQLComm =	" INSERT INTO archivo_grupo(archivo_id, grupo_id) " .
											" VALUES (" .
											$iId . "," . $iIdGrupo .
											")";
							mysql_query($strSQLComm);
							
							// Obtengo si ocurrió algún error
							if(mysql_error() != ""){
								//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
								$MsgBoxMessage = "No se pudo cargar el archivo, por favor inténtelo nuevamente.";
							}else{
								$strMail = ObtenerMails($iIdGrupo, "G", "'P','Y','L'");
							}
							break;
						case "C":
							$strSQLComm =	" INSERT INTO archivo_comision(archivo_id, comision_id) " .
											" VALUES (" .
											$iId . "," . $iIdComision .
											")";
							mysql_query($strSQLComm);
							
							// Obtengo si ocurrió algún error
							if(mysql_error() != ""){
								//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
								$MsgBoxMessage = "No se pudo cargar el archivo, por favor inténtelo nuevamente.";
							}else{
								$strMail = ObtenerMails($iIdComision, "C", "'P','Y','L'");
							}
							break;
						
					}
				
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
				$MsgBoxMessage = "No se pudo cargar el archivo, por favor inténtelo nuevamente.";
			}
		}
		else if ( GetParameter("action") == "edit" && GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id"))) && $MsgBoxMessage == "" )
		{
			
			
			// Si No hay error, direcciono al BROWSE
			if ( $strMsqlError == "" )
			{
				header("Location: $strReturnUrl");
				exit();
			}
			else
			{
				$MsgBoxMessage = "No se pudo modificar el archivo, por favor inténtelo nuevamente.";
				$bHayError = TRUE;
			}
		}
	}else{
		// Borro el registro
		if ( GetParameter("btnBorrar") != "" && GetParameter("id") && is_numeric(decrypt(GetParameter("id"))) )
		{
			include "common/inc_database_open.php";
			
			// Borro el archivo.
			$Result = mysql_query("SELECT * FROM archivo WHERE archivo_archivo <> '' AND archivo_id = " . decrypt(GetParameter("id")) . " AND usuario_id = " . SafeSql($_SESSION["UserId"]));
			if($oRs = mysql_fetch_array($Result))
			{
				BorrarArchivo($strPathFisico . $oRs["archivo_archivo"]);
				
				// Elimino el registro
				$strSQLComm = " DELETE FROM archivo " .
							  " WHERE archivo_id = " . decrypt(GetParameter("id")) . " AND usuario_id = " . SafeSql($_SESSION["UserId"]);
				mysql_query($strSQLComm);
				
				// Elimino la relacion del grupo
				$strSQLComm = " DELETE FROM archivo_grupo " .
							  " WHERE archivo_id = " . decrypt(GetParameter("id"));
				mysql_query($strSQLComm);
				// Elimino la relacion de la comision
				$strSQLComm = " DELETE FROM archivo_comision " .
							  " WHERE archivo_id = " . decrypt(GetParameter("id"));
				mysql_query($strSQLComm);
				// Elimino la relacion de la materia
				$strSQLComm = " DELETE FROM archivo_materia " .
							  " WHERE archivo_id = " . decrypt(GetParameter("id"));
				mysql_query($strSQLComm);
				// Elimino la relacion de la carrera
				$strSQLComm = " DELETE FROM archivo_carrera " .
							  " WHERE archivo_id = " . decrypt(GetParameter("id"));
				mysql_query($strSQLComm);
				// Elimino la relacion de la facultad
				$strSQLComm = " DELETE FROM archivo_facultad " .
							  " WHERE archivo_id = " . decrypt(GetParameter("id"));
				mysql_query($strSQLComm);
				// Elimino la relacion de la universidad
				$strSQLComm = " DELETE FROM archivo_universidad " .
							  " WHERE archivo_id = " . decrypt(GetParameter("id"));
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
			$BoxTitle = "Modificar archivo";
			$iId = decrypt(GetParameter("id"));
			
			

		}
		else
		{
			// Es un nuevo registro
			$BoxTitle = "Nuevo archivo";
			$strFecha = "";
			$strTitulo = GetParameter("txtTitulo");
			$strDesarrollo = GetParameter("txtDesarrollo");
			$strArchivo = "";
			$iIdComision= "0";
			if(GetParameter("idc") != "" && is_numeric(decrypt(GetParameter("idc")))){
				$iIdComision = decrypt(GetParameter("idc"));
			}
			$iIdGrupo= "0";
			if(GetParameter("idg") != "" && is_numeric(decrypt(GetParameter("idg")))){
				$iIdGrupo = decrypt(GetParameter("idg"));
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
	<? }?>

	return true;
}
</script>
<h3><?=$BoxTitle?></h3>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" enctype="multipart/form-data" method="post" name="frmForm" onsubmit="return validarForm(this);">
	<input type="hidden" name="id" value="<?=GetParameter("id")?>">
	<input type="hidden" name="action" value="<?=GetParameter("action")?>">
	<input type="hidden" name="cmbComision" value="<?=GetParameter("idc")?>">
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
		
		<tr>
			<td align="right" style="padding-bottom:10px;">Grupo:</td>
			<td><? ObtenerComboGrupo($_SESSION["UserId"], $iIdGrupo, $iIdComision, "", "--Todos--", "Y", "AcomodaDdlFiltroGrande")?></td>
		</tr>
		<tr>
			<td align="right">Título: (*)</td>
			<td><input type="text" name="txtTitulo" value="<?=$strTitulo?>"  maxlength="255" style="width:254px;"/></td>
		</tr>
		<tr>
			<td valign="top" align="right">Descripción:</td>
			<td><textarea name="txtDesarrollo" rows="15" cols="50"><?=$strDesarrollo?></textarea></td>
		</tr>
		<tr>
			<td align="right">Archivo: (*)</td>
			<td>
				<? if(GetParameter("action") == "new"){?>
					<input class="inputTxt" type="file" name="txtArchivo" value="<?=$strArchivo?>" /> <small>* Tamaño máximo 2MB</small>
				<? }else if($strArchivo != ""){?>
					<a href="descarga.php?id=<?=encrypt($iIdArchivo)?>&t=A"><img src="images/download.png" title="Descargar" style="width:40px;"/></a>
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
						<input class="boton" type="submit" name="btnActualizar" value="Actualizar"> <input type="button" name="btnBorrar" value="Borrar" onclick="ConfirmDelete('archivoform.php?btnBorrar=yes&id=<?=GetParameter("id")?>&return_url=<?=url_encode($strReturnUrl)?>','Desea realmente borrar el archivo?');" class="boton">
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