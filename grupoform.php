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
$MsgBoxMessageUsuario = "";
$BoxWidth = "";
$BoxEdit = "Off";
$BoxHelp = "Off";
$current0 = "On";

$BoxTitle = "";
$strFile = "";

// Defino variable de acceso para el INCLUDE: Access
$RequireAccess = "A|L|P|Y";

include "access.php";
include "common/func_getparameter.inc";
include "common/func_datetime.inc";
$bHayError = FALSE;

$strReturnUrl = "mismaterias.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];

$iIdComision = "0";
if(GetParameter("idc") != "" && is_numeric(decrypt(GetParameter("idc")))){
	$iIdComision = decrypt(GetParameter("idc"));
}

$iIdGrupo = "0";
if(GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id")))){
	$iIdGrupo = decrypt(GetParameter("id"));
}

	
	if ( GetParameter("btnActualizar") != "" ) 
	{
		include "common/inc_database_open.php";
		
		// Es un nuevo registro
		if ( GetParameter("action") == "new" && $MsgBoxMessage == "" && $iIdComision != "0" )
		{
			if($MsgBoxMessage == ''){
				// Armo la sentencia de INSERT
				$strSQLComm = " INSERT INTO grupo(grupo_nombre, comision_id, usuario_id, activo " .
								" ) " .
								" VALUES (" .
								"'" . SafeSql(GetParameter("txtTitulo")) . "'," .
								$iIdComision . "," .
								SafeSql($_SESSION["UserId"]) . "," . 
								"'Y'" .
								")";
				mysql_query($strSQLComm);
			
				// Obtengo si ocurrió algún error
				if(mysql_error() != ""){
					$MsgBoxMessage = "No se pudo generar el grupo, por favor inténtelo nuevamente.";
				}else{
					$iId = "";
					$Result = mysql_query("SELECT MAX(G.grupo_id) AS Id FROM grupo AS G");
					if ( $ObjRs = mysql_fetch_array($Result) )
					{
						$iId = $ObjRs["Id"];
					}
					/* Liberar conjunto de resultados */
					mysql_free_result($Result);
					
					
					// Armo la sentencia de INSERT
					$strSQLComm = " INSERT INTO usuario_grupo(grupo_id, usuario_id) " .
									" VALUES (" .
									$iId . "," .
									SafeSql($_SESSION["UserId"]) . 
									")";
					mysql_query($strSQLComm);
				
					// Obtengo si ocurrió algún error
					if(mysql_error() != ""){
						$MsgBoxMessage = "No se pudo asignar a todos los usuarios, por favor inténtelo nuevamente.";
					}
					if (GetParameter("chkUsuarios") != ""){ 
					   foreach(GetParameter("chkUsuarios") as $valor){ 
						   // Armo la sentencia de INSERT
							$strSQLComm = " INSERT INTO usuario_grupo(grupo_id, usuario_id) " .
											" VALUES (" .
											$iId . "," .
											SafeSql($valor) . 
											")";
							mysql_query($strSQLComm);
						
							// Obtengo si ocurrió algún error
							if(mysql_error() != ""){
								$MsgBoxMessage = "No se pudo asignar a todos los usuarios, por favor inténtelo nuevamente.";
							} 
					   } 
					}
				
				}
				
				
			}
			
			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $MsgBoxMessage == "" )
			{
				header("Location:" . $strReturnUrl);
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
			$strSQLComm = " UPDATE grupo SET " .
						  	" grupo_nombre='" . SafeSql(GetParameter("txtTitulo")) . "'" . 
							" WHERE grupo_id = " . SafeSql(decrypt(GetParameter("id")));
			//echo $strSQLComm;exit();
			mysql_query($strSQLComm);
			
			// Obtengo si ocurrió algún error
			$strMsqlError = mysql_error();
			if($strMsqlError == ""){
				//Borro los anteriores
				$strSQLComm = " DELETE FROM usuario_grupo " .
							  " WHERE grupo_id = " . SafeSql(decrypt(GetParameter("id")));
				mysql_query($strSQLComm);
				
				// Obtengo si ocurrió algún error
				if(mysql_error() != ""){
					$MsgBoxMessage = "No se pudo asignar a todos los usuarios, por favor inténtelo nuevamente.";
				}else{
					// Armo la sentencia de INSERT
					$strSQLComm = " INSERT INTO usuario_grupo(grupo_id, usuario_id) " .
									" VALUES (" .
									SafeSql(decrypt(GetParameter("id"))) . "," .
									SafeSql($_SESSION["UserId"]) . 
									")";
					mysql_query($strSQLComm);
				}
				
				if (GetParameter("chkUsuarios") != ""){ 
					// Obtengo si ocurrió algún error
					if(mysql_error() != ""){
						$MsgBoxMessage = "No se pudo asignar a todos los usuarios, por favor inténtelo nuevamente.";
					}else{ 
					   foreach(GetParameter("chkUsuarios") as $valor){ 
						   // Armo la sentencia de INSERT
							$strSQLComm = " INSERT INTO usuario_grupo(grupo_id, usuario_id) " .
											" VALUES (" .
											SafeSql(decrypt(GetParameter("id"))) . "," .
											SafeSql($valor) . 
											")";
							mysql_query($strSQLComm);
						
							// Obtengo si ocurrió algún error
							if(mysql_error() != ""){
								$MsgBoxMessage = "No se pudo asignar a todos los usuarios, por favor inténtelo nuevamente.";
							} 
					   }
					}
				}
			}

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
	}else{
		// Borro el registro
		if ( GetParameter("btnBorrar") != "" && GetParameter("id") && is_numeric(decrypt(GetParameter("id"))) )
		{
			include "common/inc_database_open.php";
			
			//Doy de baja al usuario en el grupo
			$strSQLComm = " DELETE FROM usuario_grupo " .
						  " WHERE grupo_id = " . SafeSql(decrypt(GetParameter("id"))) . 
						  " AND usuario_id = " . SafeSql($_SESSION["UserId"]);
			mysql_query($strSQLComm);
			
			// Obtengo si ocurrió algún error
			if(mysql_error() != ""){
				$MsgBoxMessage = "No se pudo dar de baja al grupo, por favor inténtelo nuevamente.";
			}
			
			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $MsgBoxMessage == "" )
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
		$vecUsuarios[0] = 0;
			
		if ( GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id"))) && GetParameter("action") == "edit" ) 
		{
			$BoxTitle = "Modificar grupo";
			$iId = decrypt(GetParameter("id"));
			
			$iIdGrupo = "";
			$strTitulo = "";
			$strComision = "";
			$strMateria = "";
			$strCarrera = "";
			$strFacultad = "";
			$strUniversidad = "";
			$strUsuario = "";
			
			//Valido al usuario
			$strSQLValida = " SELECT DISTINCT U.usuario_nombre, U.usuario_apellido, U.usuario_email " .
							" FROM usuario AS U " . 
							" INNER JOIN usuario_comision AS UC ON (UC.usuario_id = U.usuario_id AND UC.usuario_tipo = 'L')" .
							" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
							" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
							" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
							" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
							" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
							" WHERE UC.activo = 'Y' " .
							" AND U.activo = 'Y' " .
							" AND U.usuario_id = " . SafeSql(SafeSql($_SESSION["UserId"])) .
							" AND C.comision_id = " . SafeSql($iIdComision);
			$Result = mysql_query($strSQLValida);
			
			if(mysql_num_rows($Result)){
				
			}else{
				$MsgBoxMessage = "Datos erroneos.";
			}
			/* Liberar conjunto de resultados */
			mysql_free_result($Result);
				
			// Obtengo el grupo.
			$strSQLCommGrupo = " SELECT DISTINCT G.grupo_id, G.grupo_nombre, C.comision_titulo AS comision, C.comision_codigo, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, U.usuario_nombre, U.usuario_apellido " .
							" FROM grupo AS G " . 
							" INNER JOIN usuario AS U ON U.usuario_id = G.usuario_id " . 
							" INNER JOIN usuario_grupo AS UG ON (UG.grupo_id = G.grupo_id AND UG.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
							" INNER JOIN comision AS C ON (C.comision_id = G.comision_id AND C.activo = 'Y') " . 
							" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
							" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
							" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
							" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
							" WHERE G.activo = 'Y' " .
							" AND U.activo = 'Y' " .
							" AND G.grupo_id = " . SafeSql($iId);
			
			$Result = mysql_query($strSQLCommGrupo);
			
			if ( $oRs = mysql_fetch_array($Result) )
			{
				$iIdGrupo = $oRs["grupo_id"];
				$strTitulo = $oRs["grupo_nombre"];
				$strComision = $oRs["comision"];
				if($oRs["comision_codigo"] != ""){
					$strComision = $strComision . " (" . $oRs["comision_codigo"] . ")";
				}
				$strMateria = $oRs["materia"];
				$strCarrera = $oRs["carrera"];
				$strFacultad = $oRs["facultad"];
				$strUniversidad = $oRs["universidad"];
				$strUsuario = $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"];
				
				// Obtengo el grupo.
				$strSQLCommUsuariosGrupo = " SELECT DISTINCT U.usuario_id " .
								" FROM grupo AS G " . 
								" INNER JOIN usuario_grupo AS U ON (U.grupo_id = G.grupo_id AND U.usuario_id <> " . SafeSql($_SESSION["UserId"]) . ") " .
								" WHERE G.activo = 'Y' " .
								" AND G.grupo_id = " . SafeSql($iId);
				$ResultUsuariosGrupo = mysql_query($strSQLCommUsuariosGrupo);
				$ii = 1;
				while ( $oRs2 = mysql_fetch_array($ResultUsuariosGrupo) )
				{
					$vecUsuarios[$ii] = $oRs2["usuario_id"];
					$ii++;
				}
				/* Liberar conjunto de resultados */
				mysql_free_result($ResultUsuariosGrupo);
			}
			else
			{
				$MsgBoxMessage = "Datos erroneos.";
			}
			
			/* Liberar conjunto de resultados */
			mysql_free_result($Result);

		}
		else if($iIdComision != "0")
		{
			// Es un nuevo registro
			$BoxTitle = "Nuevo grupo";
			$strTitulo = "";
		}else{
			$MsgBoxMessage = "Datos erroneos.";
		}
		// FIN DE: Seteo variables que se mostrarán en los INPUTS
		
		if($MsgBoxMessage == ""){
			//Usuarios de la comisión
			$strSQLCommUsuarios = " SELECT DISTINCT U.usuario_nombre, U.usuario_apellido, U.usuario_email, U.usuario_id " .
							" FROM usuario AS U " . 
							" INNER JOIN usuario_comision AS UC ON (UC.usuario_id = U.usuario_id AND UC.usuario_tipo = 'L')" .
							" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
							" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
							" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
							" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
							" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
							" WHERE UC.activo = 'Y' " .
							" AND U.activo = 'Y' " .
							" AND C.comision_id = " . SafeSql($iIdComision) .
							" AND U.usuario_id <> " . SafeSql(SafeSql($_SESSION["UserId"]));
			$ResultUsuarios = mysql_query($strSQLCommUsuarios);
			if(mysql_num_rows($ResultUsuarios)){
				
			}else{
				$MsgBoxMessageUsuario = "No hay otros usuarios en la comisión.";
				/* Liberar conjunto de resultados */
				mysql_free_result($ResultUsuarios);
			}
			
		}
  
?>
<script language="JavaScript" type="text/javascript">
function validarForm(thisForm) {
	
	if(thisForm.txtTitulo.value == ''){
		alert('Debe ingresar el nombre del grupo.');
		thisForm.txtTitulo.focus();
		return false;
	}
	return true;
}


</script>
<h3><?=$BoxTitle?></h3>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" enctype="multipart/form-data" method="post" name="frmForm" onsubmit="return validarForm(this);">
	<input type="hidden" name="id" value="<?=GetParameter("id")?>">
	<input type="hidden" name="idc" value="<?=GetParameter("idc")?>">
	<input type="hidden" name="action" value="<?=GetParameter("action")?>">
	<input type="hidden" id="HidFecha" name="HidFecha">
	<input type="hidden" name="return_url" value="<?=GetParameter("return_url")?>" />
	<!-- FORMULARIO DE EDICION -->
	<?
	// Si hay un error muestro mensaje y termino
	if ($MsgBoxMessage != "") 
	{?>
		<?=$MsgBoxMessage?>
	<? 
	}else{?>
		<table border="0" cellpadding="2" cellspacing="2" align="center" style="font-size:12px;">
			<? if(GetParameter("action") == "edit" && GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id")))){?>
				<tr>
					<td align="right"><strong>Creado por:</strong></td>
					<td><?=$strUsuario?></td>
				</tr>
				<tr> 
					<td align="right" valign="top"><strong>Comisi&oacute;n:</strong></td>
					<td><?=$strUniversidad?> - <?=$strFacultad?> (<?=$strCarrera?>)<br /><?=$strMateria?> - <?=$strComision?></td>
				</tr>
			<?
			}?>
			<tr>
				<td align="right"><div class="AcomodaLabelBig">Nombre del Grupo: (*)</div></td>
				<td><input type="text" name="txtTitulo" value="<?=$strTitulo?>" class="inputTxt" maxlength="50" style="width:254px;"/></td>
			</tr>
			<tr>
				<td valign="top" colspan="2" align="center" style="font-weight:bold;font-size:12px; line-height: 22px;"><br/>Usuarios:</td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="font-size:12px; line-height: 20px;">
					<? 
					if($MsgBoxMessageUsuario == ""){
						$strPorColumna = mysql_num_rows($ResultUsuarios) / 4;
						$ii = 0;?>
						<table border="0">
							<tr>
								<td>
									<? 
									while ( $oRs = mysql_fetch_array($ResultUsuarios) ){
										if($ii >= $strPorColumna){?></td><td><? }?>
										<input type="checkbox" name="chkUsuarios[]" value="<?=$oRs["usuario_id"]?>"<? if(in_array($oRs["usuario_id"],$vecUsuarios)){?> checked="checked"<? }?>/> <?=$oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"] . " (" . $oRs["usuario_email"] . " )"?>
									<?
									}
									/* Liberar conjunto de resultados */
									mysql_free_result($ResultUsuarios);?>
								</td>
							<tr>
						</table>
					<?
					}else{?>
						<?=$MsgBoxMessageUsuario?>
					<?
					}?>
				</td>
			</tr>
			<tr class="TableBtns">
				<td valign="bottom" align="center" colspan="2">
					<?  if ( GetParameter("action") == "new" && $MsgBoxMessage == "" ) 
						{ ?>
							<input class="boton" type="submit" name="btnActualizar" value="Insertar">
					<?  } ?>
					<?  if ( GetParameter("action") == "edit" && $MsgBoxMessage == "" ) 
						{ ?>
							<input class="boton" type="submit" name="btnActualizar" value="Actualizar">
					<?  } ?>
					<input class="boton" type="button" value="Volver" onclick="self.location='<?=$strReturnUrl?>';" name="btnVolver">
				</td>
			</tr>
		</table>
		<!-- FIN DE: FORMULARIO DE EDICION -->
	<?
	}?>
</form>
<?
include "common/inc_database_close.php";
include "footer.php";
?>