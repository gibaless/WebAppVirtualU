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

$strReturnUrl = "mismensajes.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];
	
	if ( GetParameter("btnActualizar") != "" ) 
	{
		$strTipo = "";	
		$iIdUniversidad = "";
		if(GetParameter("cmbUniversidad") != "" && is_numeric(GetParameter("cmbUniversidad"))){
			$iIdUniversidad = GetParameter("cmbUniversidad");
			$strTipo = "U";
		}
		$iIdFacultad = "";
		if(GetParameter("cmbFacultad") != "" && is_numeric(GetParameter("cmbFacultad"))){
			$iIdFacultad = GetParameter("cmbFacultad");
			$strTipo = "F";
		}
		$iIdCarrera= "";
		if(GetParameter("cmbCarrera") != "" && is_numeric(GetParameter("cmbCarrera"))){
			$iIdCarrera = GetParameter("cmbCarrera");
			$strTipo = "R";
		}
		$iIdMateria= "";
		if(GetParameter("cmbMateria") != "" && is_numeric(GetParameter("cmbMateria"))){
			$iIdMateria = GetParameter("cmbMateria");
			$strTipo = "M";
		}
		$iIdComision= "";
		if(GetParameter("cmbComision") != "" && is_numeric(GetParameter("cmbComision"))){
			$iIdComision = GetParameter("cmbComision");
			$strTipo = "C";
		}
		$iIdGrupo= "";
		if(GetParameter("cmbGrupo") != "" && is_numeric(GetParameter("cmbGrupo"))){
			$iIdGrupo = GetParameter("cmbGrupo");
			$strTipo = "G";
		}
		$iIdUsuario= "";
		$strUser = GetParameter("cmbUsuario");
		if(GetParameter("cmbUsuario") != "" && is_numeric(GetParameter("cmbUsuario"))){
			$iIdUsuario = GetParameter("cmbUsuario");
			$strTipo = "P";
		}elseif(GetParameter("cmbUsuario") != ""){
			if(count($strUser) == 1 && $strUser[0] == ""){
			
			}else{
				$iIdUsuario = GetParameter("cmbUsuario");
				$strTipo = "P";
			}
		}
		
		if ( $MsgBoxMessage == "" && $strTipo != "" )
		{
			include "common/inc_database_open.php";
			
			// Armo la sentencia de INSERT
			$strSQLComm = " INSERT INTO mensaje(usuario_id, mensaje_fecha, mensaje_titulo, mensaje_mensaje, mensaje_tipo, mensaje_activo) " .
							" VALUES (" .
							SafeSql($_SESSION["UserId"]) . "," . 
							"NOW()," .
							"'" . SafeSql(GetParameter("txtTitulo")) . "'," .
							"'" . SafeSql(GetParameter("txtMensaje")) . "'," .
							"'" . SafeSql($strTipo) . "'," .
							" 'Y'" .
							")";
			mysql_query($strSQLComm);

			// Obtengo si ocurrió algún error
			if(mysql_error() != ""){
				//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
				$MsgBoxMessage = "No se pudo enviar el mensaje, por favor inténtelo nuevamente.";
			}else{
				$iId = "";
				$Result = mysql_query("SELECT MAX(mensaje_id) AS Id FROM mensaje");
				if ( $ObjRs = mysql_fetch_array($Result) )
				{
					$iId = $ObjRs["Id"];
				}
				/* Liberar conjunto de resultados */
				mysql_free_result($Result);
				
				$strNombreMensaje = "VirtualU";
				$strMail = "";
				$strTituloMensaje = "Nuevo Mensaje";
				$strMensaje = "Usted tiene un nuevo mensaje. Para visualizarlo ingrese <a href=" . $SITE_URL . "/mensajever.php?id=" . encrypt($iId) . ">aquí</a>.<br>Si no puede ingresar al link, copie y pegue en su navegador la siguiente url: <b>". $SITE_URL . "/mensajever.php?id=" . encrypt($iId) . "</b>";
				
				switch($strTipo){
					case "U"://Mensaje para Universidad
						// Armo la sentencia de INSERT de la relación
						$strSQLComm = " INSERT INTO mensaje_universidad(mensaje_id, universidad_id) " .
										" VALUES (" .
										SafeSql($iId) . "," .
										$iIdUniversidad .
										")";
						mysql_query($strSQLComm);
			
						// Obtengo si ocurrió algún error
						if(mysql_error() != ""){
							//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
							$MsgBoxMessage = "No se pudo enviar el mensaje, por favor inténtelo nuevamente.";
						}else{
							$strMail = ObtenerMails($iIdUniversidad, "U", "'P','Y','L'");
						}
						break;
					case "F"://Mensaje para Facultad
						// Armo la sentencia de INSERT de la relación
						$strSQLComm = " INSERT INTO mensaje_facultad(mensaje_id, facultad_id) " .
										" VALUES (" .
										SafeSql($iId) . "," .
										$iIdFacultad .
										")";
						mysql_query($strSQLComm);
			
						// Obtengo si ocurrió algún error
						if(mysql_error() != ""){
							//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
							$MsgBoxMessage = "No se pudo enviar el mensaje, por favor inténtelo nuevamente.";
						}else{
							$strMail = ObtenerMails($iIdFacultad, "F", "'P','Y','L'");
						}
						break;
					case "R"://Mensaje para Carrera
						// Armo la sentencia de INSERT de la relación
						$strSQLComm = " INSERT INTO mensaje_carrera(mensaje_id, carrera_id) " .
										" VALUES (" .
										SafeSql($iId) . "," .
										$iIdCarrera .
										")";
						mysql_query($strSQLComm);
			
						// Obtengo si ocurrió algún error
						if(mysql_error() != ""){
							//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
							$MsgBoxMessage = "No se pudo enviar el mensaje, por favor inténtelo nuevamente.";
						}else{
							$strMail = ObtenerMails($iIdCarrera, "R", "'P','Y','L'");
						}
						break;
					case "M"://Mensaje para Materia
						// Armo la sentencia de INSERT de la relación
						$strSQLComm = " INSERT INTO mensaje_materia(mensaje_id, materia_id) " .
										" VALUES (" .
										SafeSql($iId) . "," .
										$iIdMateria .
										")";
						mysql_query($strSQLComm);
			
						// Obtengo si ocurrió algún error
						if(mysql_error() != ""){
							//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
							$MsgBoxMessage = "No se pudo enviar el mensaje, por favor inténtelo nuevamente.";
						}else{
							$strMail = ObtenerMails($iIdMateria, "M", "'P','Y','L'");
						}
						break;
					case "C"://Mensaje para Comision
						// Armo la sentencia de INSERT de la relación
						$strSQLComm = " INSERT INTO mensaje_comision(mensaje_id, comision_id) " .
										" VALUES (" .
										SafeSql($iId) . "," .
										$iIdComision .
										")";
						mysql_query($strSQLComm);
			
						// Obtengo si ocurrió algún error
						if(mysql_error() != ""){
							//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
							$MsgBoxMessage = "No se pudo enviar el mensaje, por favor inténtelo nuevamente.";
						}else{
							$strMail = ObtenerMails($iIdComision, "C", "'P','Y','L'");
						}
						break;
					case "G"://Mensaje para Grupo
						// Armo la sentencia de INSERT de la relación
						$strSQLComm = " INSERT INTO mensaje_grupo(mensaje_id, grupo_id) " .
										" VALUES (" .
										SafeSql($iId) . "," .
										$iIdGrupo .
										")";
						mysql_query($strSQLComm);
			
						// Obtengo si ocurrió algún error
						if(mysql_error() != ""){
							//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
							$MsgBoxMessage = "No se pudo enviar el mensaje, por favor inténtelo nuevamente.";
						}else{
							$strMail = ObtenerMails($iIdGrupo, "G", "'P','Y','L'");
						}
						break;
					case "P"://Mensaje para Usuario
						if (is_array($iIdUsuario)){ 
						   $ii = 0;
						   foreach($iIdUsuario as $valor){ 
							   if($valor != "" && is_numeric($valor)){
								   // Armo la sentencia de INSERT de la relación
									$strSQLComm = " INSERT INTO mensaje_usuario(mensaje_id, usuario_id) " .
													" VALUES (" .
													SafeSql($iId) . "," .
													SafeSql($valor) .
													")";
									mysql_query($strSQLComm);
								}
								if($ii != 0){
									$strMail = $strMail . ",";
								}
								$strMail = $strMail . ObtenerMails($valor, "P", "'P','Y','L'");
								$ii ++;
						   } 
						}else{
							// Armo la sentencia de INSERT de la relación
							$strSQLComm = " INSERT INTO mensaje_usuario(mensaje_id, usuario_id) " .
											" VALUES (" .
											SafeSql($iId) . "," .
											$iIdUsuario .
											")";
							mysql_query($strSQLComm);
						}
						// Obtengo si ocurrió algún error
						if(mysql_error() != ""){
							//echo mysql_error() . '<br><br>' . $strSQLComm;exit();
							$MsgBoxMessage = "No se pudo enviar el mensaje, por favor inténtelo nuevamente.";
						}else{
							$strMail = ObtenerMails($iIdUsuario, "P", "'P','Y','L'");
						}
						break;
					
				}//FIN: Switch case
				
			}
			include "common/inc_database_close.php";
			
			// Si No hay error, direcciono al BROWSE
			if ( $MsgBoxMessage == "" )
			{
				include "enviarmail.php";
				?><script language="javascript" type="text/javascript">EnviarMail('<?=$strNombreMensaje?>', '<?=$strMail?>', '<?=$strTituloMensaje?>', '<?=$strMensaje?>' );</script>
				<script language="javascript" type="text/javascript">self.location='<?=$strReturnUrl?>';</script><?
				//header("Location: $strReturnUrl");
				exit();
			}
			
		}
	}
	// FIN DE: Si se agrega un nuevo registro
include "header.php";
include "common/inc_database_open.php";

$strReadOnly = "";
$strUsuario = "";
$strTitulo = "";
$strMensaje = "";
$strFecha = "";
$strTipo = "";
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
$iIdGrupo= "0";
if(GetParameter("idg") != "" && is_numeric(decrypt(GetParameter("idg")))){
	$iIdGrupo = decrypt(GetParameter("idg"));
}
$iIdUsuario= "0";
if(GetParameter("idp") != "" && is_numeric(decrypt(GetParameter("idp")))){
	$iIdUsuario = decrypt(GetParameter("idp"));
}

if(GetParameter("id") != "" && is_numeric(decrypt(GetParameter("id")))){
	//Obtengo el mensaje
	$strSQLCommMensajes = " SELECT mensaje_id, mensaje_fecha, mensaje_titulo, mensaje_mensaje, mensaje_tipo, usuario_id, usuario_nombre, usuario_apellido, " . 
					" grupo, comision, materia, carrera, facultad, universidad, grupo_id, comision_id, materia_id, carrera_id, facultad_id, universidad_id " .
					" FROM " .  
					" ((SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'PRIVADO' AS grupo, 'PRIVADO' AS comision, 'PRIVADO' AS materia, 'PRIVADO' AS carrera, 'PRIVADO' AS facultad, 'PRIVADO' AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, 0 AS carrera_id, 0 AS facultad_id, 0 AS universidad_id " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON (U.usuario_id = M.usuario_id "; 
	if(GetParameter("action") == "edit"){
		$strSQLCommMensajes = $strSQLCommMensajes .	"AND U.usuario_id = " . SafeSql($_SESSION["UserId"]); 
	}
	$strSQLCommMensajes = $strSQLCommMensajes .	") " . 
						" INNER JOIN mensaje_usuario AS MU ON (M.mensaje_id = MU.mensaje_id  "; //Mensajes Privados.
	if(GetParameter("action") == "new"){
		$strSQLCommMensajes = $strSQLCommMensajes .	"AND MU.usuario_id = " . SafeSql($_SESSION["UserId"]); 
	}
	$strSQLCommMensajes = $strSQLCommMensajes .	") " . 
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" G.grupo_nombre AS grupo, C.comision_titulo AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, G.grupo_id AS grupo_id, C.comision_id AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_grupo AS MG ON M.mensaje_id = MG.mensaje_id " . //Mensajes de Grupos 
					" INNER JOIN grupo AS G ON (G.grupo_id = MG.grupo_id AND G.activo = 'Y') " . 
					" INNER JOIN usuario_grupo AS UG ON (UG.grupo_id = G.grupo_id AND UG.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " . 
					" INNER JOIN comision AS C ON (C.comision_id = G.comision_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
					" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
					" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
					" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
					" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'TODOS' AS grupo, C.comision_titulo AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, C.comision_id AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_comision AS MC ON M.mensaje_id = MC.mensaje_id " .  //Mensajes de Comisiones
					" INNER JOIN comision AS C ON (C.comision_id = MC.comision_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " . 
					" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
					" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
					" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
					" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
	
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'TODOS' AS grupo, 'TODAS' AS comision, MA.materia_titulo AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, MA.materia_id AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_materia AS MM ON M.mensaje_id = MM.mensaje_id " .  //Mensajes de Materias
					" INNER JOIN comision AS C ON (C.materia_id = MM.materia_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
					" INNER JOIN materia AS MA ON (MA.materia_id = MM.materia_id AND MA.activo = 'Y') " . 
					" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
					" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
					" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'TODAS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, R.carrera_titulo AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, R.carrera_id AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_carrera AS MR ON M.mensaje_id = MR.mensaje_id " .  //Mensajes de Carreras
					" INNER JOIN materia AS MA ON (MA.carrera_id = MR.carrera_id AND MA.activo = 'Y') " . 
					" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
					" INNER JOIN carrera AS R ON (R.carrera_id = MR.carrera_id AND R.activo = 'Y') " . 
					" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
					" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'TODOS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, 'TODAS' AS carrera, F.facultad_titulo AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, 0 AS carrera_id, F.facultad_id AS facultad_id, UN.universidad_id AS universidad_id " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_facultad AS MF ON M.mensaje_id = MF.mensaje_id " .  //Mensajes de Facultades
					" INNER JOIN carrera AS R ON (R.facultad_id = MF.facultad_id AND R.activo = 'Y') " . 
					" INNER JOIN materia AS MA ON (MA.carrera_id = R.carrera_id AND MA.activo = 'Y') " . 
					" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
					" INNER JOIN facultad AS F ON (F.facultad_id = MF.facultad_id AND F.activo = 'Y') " . 
					" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
					" )UNION( " .
					" SELECT M.mensaje_id, M.mensaje_fecha, M.mensaje_titulo, M.mensaje_mensaje, M.mensaje_tipo, U.usuario_id, U.usuario_nombre, U.usuario_apellido, " . 
					" 'TODOS' AS grupo, 'TODAS' AS comision, 'TODAS' AS materia, 'TODAS' AS carrera, 'TODAS' AS facultad, UN.universidad_titulo AS universidad, 0 AS grupo_id, 0 AS comision_id, 0 AS materia_id, 0 AS carrera_id, 0 AS facultad_id, UN.universidad_id AS universidad_id " .
					" FROM mensaje AS M " . 
					" INNER JOIN usuario AS U ON U.usuario_id = M.usuario_id " . 
					" INNER JOIN mensaje_universidad AS MU ON M.mensaje_id = MU.mensaje_id " .  //Mensajes de Universidades
					" INNER JOIN facultad AS F ON (F.universidad_id = MU.universidad_id AND F.activo = 'Y') " . 
					" INNER JOIN carrera AS R ON (R.facultad_id = F.facultad_id AND R.activo = 'Y') " . 
					" INNER JOIN materia AS MA ON (MA.carrera_id = R.carrera_id AND MA.activo = 'Y') " . 
					" INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
					" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($_SESSION["UserId"]) . ") " .
					" INNER JOIN universidad AS UN ON (UN.universidad_id = MU.universidad_id AND UN.activo = 'Y') " . 
					" WHERE M.mensaje_activo = 'Y' " .
					" AND U.activo = 'Y' " .
					" )) AS CTOTAL " .
					" WHERE CTOTAL.mensaje_id = " . SafeSql(decrypt(GetParameter("id")));
		$Result = mysql_query($strSQLCommMensajes);  
		if(mysql_num_rows($Result)){
			if($oRs = mysql_fetch_array($Result)){
				$strUsuario = $oRs["usuario_nombre"] . " " . $oRs["usuario_apellido"];
				$strTitulo = $oRs["mensaje_titulo"];
				$strFecha = DateTimeFormat($oRs["mensaje_fecha"]);
				$strMensaje = chr(13) . chr(13) . chr(13) . chr(13) . "------------------------------------" . chr(13) . "Enviado " . $strFecha . " por " . $strUsuario . chr(13) . chr(13) . $oRs["mensaje_titulo"] . chr(13) . chr(13) . $oRs["mensaje_mensaje"];
				if(GetParameter("action") == "new"){
					$strTipo = $oRs["mensaje_tipo"];
					$strTitulo = "Re: " . $strTitulo;
					$iIdUniversidad = $oRs["universidad_id"];
					$iIdFacultad = $oRs["facultad_id"];
					$iIdCarrera = $oRs["carrera_id"];
					$iIdMateria = $oRs["materia_id"];
					$iIdComision = $oRs["comision_id"];
					$iIdGrupo = $oRs["grupo_id"];
					$iIdUsuario = $oRs["usuario_id"];
					//$strReadOnly = "onmouseover='this.disabled=true;' onmouseout='this.disabled=false;' ";
				}else{
					$strTitulo = "Rv: " . $strTitulo;
				}
			}
		}
		/* Liberar conjunto de resultados */
		mysql_free_result($Result);
}
?>
<script language="JavaScript" type="text/javascript">
function validarForm(thisForm) {
	
	<? if($strTipo != "P"){?>
		if(thisForm.cmbUniversidad.value == ''){
			alert('Debe seleccionar al menos la universidad.');
			thisForm.cmbUniversidad.focus();
			return false;
		}
	<?
	}?>
	if(thisForm.txtTitulo.value == ''){
		alert('Debe ingresar el título.');
		thisForm.txtTitulo.focus();
		return false;
	}
	if(thisForm.txtMensaje.value == ''){
		alert('Debe ingresar el mensaje.');
		thisForm.txtMensaje.focus();
		return false;
	}
	
	return true;
}
</script>
<h2>Nuevo Mensaje</h2>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm" onsubmit="return validarForm(this);">
	<input type="hidden" id="HidFecha" name="HidFecha">
	<input type="hidden" name="id" value="<?=GetParameter("id")?>" />
	<input type="hidden" name="action" value="<?=GetParameter("action")?>" />
	<input type="hidden" name="hdTipo" value="<?=$strTipo?>" />
	<input type="hidden" name="return_url" value="<?=GetParameter("return_url")?>" />
	<!-- FORMULARIO DE EDICION -->
	<?
	// Si hay un error muestro mensaje y termino
	if ($MsgBoxMessage != "") 
	{?>
		<?=$MsgBoxMessage?>
	<? }?>
	<div class="Formulario">
		<? if($strTipo != "P"){?>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Universidad: (*)
			</div>
			<div id="dvUniversidades"><? ObtenerComboUniversidad($_SESSION["UserId"], $iIdUniversidad, $strReadOnly . "onchange=\"TraerFacultades('" . $iIdFacultad . "','--Todas--','Y');\"", "--Seleccione--", "Y", "AcomodaDdlFiltroGrande")?></div>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Facultad:
			</div>
			<div id="dvFacultades"><? ObtenerComboFacultad($_SESSION["UserId"], $iIdFacultad, $iIdUniversidad, $strReadOnly . "onchange=\"TraerCarreras('" . $iIdCarrera . "','--Todas--','Y');\"", "--Todas--", "Y", "AcomodaDdlFiltroGrande")?></div>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Carrera:
			</div>
			<div id="dvCarreras"><? ObtenerComboCarrera($_SESSION["UserId"], $iIdCarrera, $iIdFacultad, $strReadOnly . "onchange=\"TraerMaterias('" . $iIdMateria . "','--Todas--','Y');\"", "--Todas--", "Y", "AcomodaDdlFiltroGrande")?></div>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Materia:
			</div>
			<div id="dvMaterias"><? ObtenerComboMateria($_SESSION["UserId"], $iIdMateria, $iIdCarrera, $strReadOnly . "onchange=\"TraerComisiones('" . $iIdComision . "','--Todos--','Y');\"", "--Todas--",  "Y", "AcomodaDdlFiltroGrande")?></div>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Comisi&oacute;n:
			</div>
			<div id="dvComisiones"><? ObtenerComboComision($_SESSION["UserId"], $iIdComision, $iIdMateria, $strReadOnly . "onchange=\"TraerGrupos('" . $iIdGrupo . "','--Todos--','Y');TraerUsuariosComision('" . $iIdUsuario . "','--Todos--','Y');\"", "--Todas--", "Y", "AcomodaDdlFiltroGrande")?></div>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Grupo:
			</div>
			<? ObtenerComboGrupo($_SESSION["UserId"], $iIdGrupo, $iIdComision, $strReadOnly . "onchange=\"TraerUsuarios('" . $iIdUsuario . "','--Todos--','Y');\"", "--Todos--", "Y", "AcomodaDdlFiltroGrande")?>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">
				Usuarios:
			</div>
			<? 
			if($iIdGrupo != "0"){
				ObtenerComboUsuario($_SESSION["UserId"], "", $iIdGrupo, $strReadOnly, "--Todos--", "Y", "AcomodaDdlFiltroGrande");
			}else{
				ObtenerComboUsuarioPorComision($_SESSION["UserId"], "", $iIdComision, $strReadOnly, "--Todos--", "Y", "AcomodaDdlFiltroGrande");
			}?>
		</div>
		<?
		}else{?>
			<input type="hidden" name="cmbUsuario" value="<?=$iIdUsuario?>" />
			<div class="separaModulo">
				<div class="AcomodaLabel">
					A:
				</div>
				<?=$strUsuario?>
			</div>
		<?
		}?>
		<div class="separaModulo">
			<div class="AcomodaLabel">Título: (*)</div>
			<td><input type="text" name="txtTitulo" value="<?=$strTitulo?>" class="inputTxt" maxlength="255" style="width:254px;" /></td>
		</div>
		<div class="separaModulo">
			<div class="AcomodaLabel">Mensaje: (*)</div>
			<td><textarea name="txtMensaje" rows="15" cols="60"><?=$strMensaje?></textarea></td>
		</div>
	</div>
	<div class="PosicionBotones">
		<input class="boton" type="submit" name="btnActualizar" value="Enviar">
		<input class="boton" type="button" value="Volver" onclick="self.location='<?=$strReturnUrl?>';" name="btnVolver">
	</div>
	<!-- FIN DE: FORMULARIO DE EDICION -->
</form>
<?
include "common/inc_database_close.php";
include "footer.php";
?>
