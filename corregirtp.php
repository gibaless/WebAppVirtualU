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

$RequireAccess = "A|L|P|Y";
include "access.php";

$MsgBoxMessage = '';

$iId = "0";
if(GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id")))){
	$iId = decrypt(GetParameter("id"));
}

$iPag = "0";
if(GetParameter("p") != "" && is_numeric(GetParameter("p"))){
	$iPag = GetParameter("p");
}

   
$vecArchivos = "";

// ARMO RETURN URL
$strReturnUrl = "mistps.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];



if ( GetParameter("btnActualizar") != "" ) 
{
	include "common/inc_database_open.php";
	
	// Es un nuevo registro
	if ( GetParameter("action") == "new" )
	{
		if($MsgBoxMessage == ''){
			// Armo la sentencia de INSERT
			$strSQLComm = " INSERT INTO nota(entrega_id, usuario_id, nota_nota, nota_observacion, nota_fechacorreccion " .
							" ) " .
							" VALUES (" .
							SafeSql($iId) . "," .
							SafeSql($_SESSION["UserId"]) . "," .
							"'" . SafeSql(GetParameter("txtNota")) . "'," .
							"'" . SafeSql(GetParameter("txtDesarrollo")) . "'," .
							"NOW()" .
							")";
			mysql_query($strSQLComm);
		
			// Obtengo si ocurrió algún error
			if(mysql_error() != ""){
				//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
				$MsgBoxMessage = "No se pudo cargar la corrección, por favor inténtelo nuevamente.";
			}else{
				// ARMO LA SENTENCIA DE UPDATE
				$strSQLComm = " UPDATE entrega SET " .
								" entrega_estado='C'" .
								" WHERE entrega_id = " . SafeSql($iId);
				//echo $strSQLComm;exit();
				mysql_query($strSQLComm);
				
				// Obtengo si ocurrió algún error
				$strMsqlError = mysql_error();
				
				$iIdGrupo = "0";
				
				$Result = mysql_query("SELECT grupo_id AS Id FROM entrega WHERE entrega_id = " . SafeSql($iId));
				if ( $ObjRs = mysql_fetch_array($Result) )
				{
					$iIdGrupo = $ObjRs["Id"];
				}
				/* Liberar conjunto de resultados */
				mysql_free_result($Result);
				
				$strNombreMensaje = "VirtualU";
				$strMail = "";
				$strTituloMensaje = "Nueva Corrección";
				$strMensaje = "Se ha corregido un nuevo TP. Para visualizarlo ingrese <a href=" . $SITE_URL . "/corregirtp.php?id=" . encrypt($iId) . ">aquí</a>.<br>Si no puede ingresar al link, copie y pegue en su navegador la siguiente url: <b>". $SITE_URL . "/corregirtp.php?id=" . encrypt($iId) . "</b>";
				$strMail = ObtenerMails($iIdGrupo, "G", "'L'");
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
	else if ( GetParameter("action") == "edit" )
	{
		
		// ARMO LA SENTENCIA DE UPDATE
		$strSQLComm = " UPDATE nota SET " .
						" nota_fechacorreccion=NOW()," .	
						" usuario_id = " . SafeSql($_SESSION["UserId"]) . "," .
						" nota_nota='" . SafeSql(GetParameter("txtNota")) . "'," .
						" nota_observacion='" . SafeSql(GetParameter("txtDesarrollo")) . "'" .
						" WHERE entrega_id = " . SafeSql($iId);
		//echo $strSQLComm;exit();
		mysql_query($strSQLComm);
		
		// Obtengo si ocurrió algún error
		$strMsqlError = mysql_error();
		
		$iIdGrupo = "0";
				
		$Result = mysql_query("SELECT grupo_id AS Id FROM entrega WHERE entrega_id = " . SafeSql($iId));
		if ( $ObjRs = mysql_fetch_array($Result) )
		{
			$iIdGrupo = $ObjRs["Id"];
		}
		/* Liberar conjunto de resultados */
		mysql_free_result($Result);
		
		$strNombreMensaje = "VirtualU";
		$strMail = "";
		$strTituloMensaje = "Recorrección";
		$strMensaje = "Se ha recorregido un TP. Para visualizarlo ingrese <a href=" . $SITE_URL . "/corregirtp.php?id=" . encrypt($iId) . ">aquí</a>.<br>Si no puede ingresar al link, copie y pegue en su navegador la siguiente url: <b>". $SITE_URL . "/corregirtp.php?id=" . encrypt($iId) . "</b>";
		$strMail = ObtenerMails($iIdGrupo, "G", "'L'");
		
		include "common/inc_database_close.php";
		
		// Si No hay error, direcciono al BROWSE
		if ( $strMsqlError == "" )
		{
			include "enviarmail.php";
			?><script language="javascript" type="text/javascript">EnviarMail('<?=$strNombreMensaje?>', '<?=$strMail?>', '<?=$strTituloMensaje?>', '<?=$strMensaje?>' );</script>
			<script language="javascript" type="text/javascript">self.location='<?=$strReturnUrl?>';</script><?
			exit();
		}
		else
		{
			$MsgBoxMessage = "No se pudo modificar la corrección, por favor inténtelo nuevamente.";
			$bHayError = TRUE;
		}
	}
}

include "common/inc_database_open.php";
	
// Obtengo la entrega.
$strSQLCommEntregas = " SELECT DISTINCT TP.tp_titulo, E.tp_id, E.entrega_id, E.grupo_id, E.entrega_version, E.entrega_archivo, E.entrega_observacion, E.entrega_fechacreacion, " .
				" E.entrega_fechaentrega, E.entrega_estado, E.activo, N.nota_nota, N.nota_observacion, UR.usuario_nombre, UR.usuario_apellido, G.grupo_nombre, UC.usuario_tipo, UG.usuario_id AS usuario_id_grupo " . 
				" FROM entrega AS E " . 
				" INNER JOIN trabajo_practico AS TP ON (E.tp_id = TP.tp_id AND TP.activo = 'Y') " . 
				" INNER JOIN grupo AS G ON (G.grupo_id = E.grupo_id AND G.activo = 'Y') " . 
				" LEFT JOIN usuario_grupo AS UG ON (UG.grupo_id = G.grupo_id AND UG.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
				" INNER JOIN usuario_comision AS UC ON (UC.comision_id = G.comision_id AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
				" LEFT JOIN nota AS N ON N.entrega_id = E.entrega_id " . 
				" LEFT JOIN usuario AS UR ON (UR.usuario_id = N.usuario_id AND UR.activo = 'Y') " . 
				" WHERE UC.activo = 'Y' " .
				" AND E.activo = 'Y' " .
				" AND E.entrega_id = " . SafeSql($iId);
//echo($strSQLCommEntregas);exit();
$Result = mysql_query($strSQLCommEntregas);

include "header.php"; 
?>
<script language="javascript" type="text/javascript">
	function ConfirmCambiar(strUrl, strMensaje, strEdit){
		var res = true;
		if(strEdit == 'Y'){
			res = confirm(strMensaje);
		}
		if(res){
			self.location = strUrl;
		}
	}
</script>
<h2>Correcci&oacute;n</h2>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm" onsubmit="return validarForm(this);">
<table border="0" cellpadding="1" cellspacing="1">
	
	<?
	$bHayDatos = false;
	if(mysql_num_rows($Result)){
		if($oRs = mysql_fetch_array($Result)){
			if($oRs["usuario_tipo"] == "P" || $oRs["usuario_tipo"] == "Y" || $oRs["usuario_id_grupo"] != ""){
				$bHayDatos = true;
				$vecArchivos = lee_archivos("C:\\Archivos de programa\\EasyPHP-5.3.8.0\\www\\VirtualU\\entregas\\Imagenes\\",$oRs["entrega_archivo"]);//Obtengo las imagenes
				$strEdit= "N";
								
				if($oRs["usuario_tipo"] == "P" || $oRs["usuario_tipo"] == "Y"){
					$strEdit = "Y";
				}
				$strImg = "";
				if($iPag < count($vecArchivos)){
					$strImg = $vecArchivos[$iPag];
				}else{
					$iPag = 0;
				}
				$strDetalle = $oRs["nota_observacion"];
				$strNota = $oRs["nota_nota"];
				$strAction = "new";
				if($strNota != ""){
					$strAction = "edit";
				}
				?><tr><td colspan="2" style="font-weight:bold;">
					<small>Para poder ver esta página necesita Adobe Flash Player. Desc&aacute;rguelo<a target="_blank" href="http://get.adobe.com/es/flashplayer/"> >> aqui << </a></small>
					<br/>
					<small><a href="corregirtp.php?id=<?=GetParameter("id")?>&p=<?=$iPag?>&return_url=<?=urlencode($strReturnUrl)?>">Si no puede visualizar la imagen del documento haga click aqu&iacute;</a></small>
					<?
					$strSQLCorreccion = "SELECT U.usuario_nombre, U.usuario_apellido " .
										" FROM correccion AS C " .
										" INNER JOIN usuario AS U ON U.usuario_id = C.usuario_id " .
										" WHERE entrega_id = " . SafeSql($iId) . 
										" AND correccion_pagina = " . $iPag .
										" LIMIT 1 ";
					$Result2 = mysql_query($strSQLCorreccion);
					if(mysql_num_rows($Result2)){
						if($oRs2 = mysql_fetch_array($Result2)){
							?><br /><br /><small>P&aacute;gina corregida por <?=$oRs2["usuario_nombre"] . " " . $oRs2["usuario_apellido"]?></small><?
						}
					}
					/* Liberar conjunto de resultados */
					mysql_free_result($Result2);?>
					
				</td></tr>
				<tr>
					<td style="border:solid 1px #000000;">	
						<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
								id="virtualu" width="874" height="1056"
								codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
								<param name="movie" value="virtualu.swf?id=<?=$oRs["entrega_id"]?>&img=<?=$strImg?>&p=<?=$iPag?>&idu=<?=$_SESSION["UserId"]?>&edit=<?=$strEdit?>" />
								<param name="quality" value="high" />
								<param name="bgcolor" value="#000000" />
								<param name="allowScriptAccess" value="sameDomain" />
								<embed src="virtualu.swf?id=<?=$oRs["entrega_id"]?>&img=<?=$strImg?>&p=<?=$iPag?>&idu=<?=$_SESSION["UserId"]?>&edit=<?=$strEdit?>" quality="high" bgcolor="#000000"
									width="874" height="1056" name="virtualu" align="middle"
									play="true"
									loop="false"
									quality="high"
									allowScriptAccess="sameDomain"
									type="application/x-shockwave-flash"
									pluginspage="http://www.adobe.com/go/getflashplayer">
								</embed>
						</object>
					</td>
					<td valign="top" align="left">
						<h3 style="margin-left:0;">P&aacute;ginas</h3>
						<? for($i=0;$i<count($vecArchivos);$i++){?>
							<a href="javascript://;" onclick="ConfirmCambiar('corregirtp.php?id=<?=GetParameter("id")?>&p=<?=$i?>&return_url=<?=urlencode($strReturnUrl)?>','Usted cabiará de página. Asegúrese de haber guardado los cambios de la misma.\nDesea cambiar de página?', '<?=$strEdit?>');">P&aacute;gina <?=($i + 1)?></a><br />
						<?
						}?>
						
					</td>
				</tr>
			<?
			}else{?>
	
				<p style="margin-left:30px; font-size:11px;"><img src="images/errormsg.png" title="Error" style=" vertical-align:middle;"/>&nbsp;&nbsp;&nbsp;		
		Usted no tiene permisos para visualizar la corrección.</p>
		
	<?		}
		}
	}else{?>
		<p style="margin-left:30px; font-size:11px;"><img src="images/errormsg.png" title="Error" style=" vertical-align:middle;"/>&nbsp;&nbsp;&nbsp;		
		Usted no tiene permisos para visualizar la corrección.</p>
	<?
	}
	/* Liberar conjunto de resultados */
	mysql_free_result($Result);?>
	<tr>
		<td colspan="2" align="left">
			<? if($bHayDatos){
					if($strEdit == "Y"){?>
						<script language="JavaScript" type="text/javascript">
							function validarForm(thisForm) {
								if(confirm('Usted va a cerrar la corrección. Asegúrese de haber guardado los cambios de la página actual.\nDesea cerrar la corrección?')){
									if(thisForm.txtNota.value == ''){
										alert('Debe ingresar la nota.');
										thisForm.txtNota.focus();
										return false;
									}
									return true;
								}else{
									return false;
								}
							}
						</script>
						<input type="hidden" name="id" value="<?=GetParameter("id")?>" />
						<input type="hidden" name="action" value="<?=$strAction?>" />
						<input type="hidden" name="return_url" value="<?=$strReturnUrl?>" />
						Nota:
						<br />
						<input type="text" id="txtNota" name="txtNota" value="<?=$strNota?>" maxlength="150" style="width:400px;"/>
						<br />
						Observaci&oacute;n:
						<br />
						<textarea name="txtDesarrollo" rows="15" cols="50"><?=$strDetalle?></textarea>
						<br />
						<br />
						<input class="boton" type="submit" name="btnActualizar" value="Cerrar Corrección">
					<?
					}else{?>
						<strong>Nota:</strong> <?=$strNota?>
						<br />
						<strong>Observaci&oacute;n:</strong> <?=str_replace(chr(13), "<br>", $strDetalle)?>
						<br />
						<br />
			<?		} ?>
						<input class="boton" type="button" value="Volver" onclick="self.location='<?=$strReturnUrl?>';" name="btnVolver">
			<?
			}?>
		</td>
	</tr>
</table>
</form>
<br />

<? 
include "common/inc_database_close.php";
include "footer.php"; ?>
