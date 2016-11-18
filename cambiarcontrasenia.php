<?
session_start();
header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
header("Expires: -100000"); //Expira pÃ¡gina

/* INCLUYO ARCHIVO DE CONFIGURACIÃ“N. */
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "common/func_getparameter.inc"; // AGREGO EL GETPARAMETER.
include "common/func_datetime.inc"; // AGREGO EL GETPARAMETER.

$RequireAccess = "A|L|P|Y";
include "access.php";

$MsgBoxMessage = "";
$strReturnUrl = "cambiarcontrasenia.php";
if(isset($_REQUEST["return_url"]) && $_REQUEST["return_url"] != "")
	$strReturnUrl = $_REQUEST["return_url"];

	
		if ( isset($_POST["btnActualizar"]) ) 
		{
		
			include "common/inc_database_open.php";
		
			$strPassword = "";
			if(GetParameter("txtPassword2") != ""){
				$strPassword = md5(GetParameter("txtPassword2"));
			}
			$strEmail = "";
			if(GetParameter("txtEmail") != ""){
				$strEmail = GetParameter("txtEmail");
			}
			
			$iId = $_SESSION["UserId"];
			$strCommQuery = "SELECT U.usuario_email, U.usuario_password " .
					" FROM usuario AS U " . 
					" WHERE U.activo = 'Y' AND U.usuario_id = " . SafeSql($iId) . " AND usuario_password = '" . md5(SafeSql(GetParameter("txtPassword"))) . "'";

			$Result = mysql_query($strCommQuery);
			if ( $ObjRs = mysql_fetch_array($Result) )
			{
				// ARMO LA SENTENCIA DE UPDATE
				$strSQLComm = " UPDATE usuario SET " .
						" usuario_password = '" . SafeSql($strPassword) . "' " .
						" WHERE usuario_id = " . SafeSql($_POST["id"]);
			
				mysql_query($strSQLComm);
				
				// Obtengo si ocurrio algun error
				$strMsqlError = mysql_error();
				
				include "common/inc_database_close.php";
				
				// Si No hay error, direcciono al BROWSE
				if ( $strMsqlError == "" )
				{
					$MsgBoxMessage = "Sus datos han sido modificados correctamente.";
				}else{
					
					$MsgBoxMessage = "Ha ocurrido un error en la actualizacion de su contraseña.";
	
				}
			}else{
				$MsgBoxMessage = "Su usuario y/o contraseña son incorrectas.";
			}
			/* Liberar conjunto de resultados */
			mysql_free_result($Result);	
		}else{

		include "common/inc_database_open.php";
		
		// Seteo variables que se mostraran los INPUTS
		
		
			$iId = $_SESSION["UserId"];
			$strCommQuery = "SELECT U.usuario_email, U.usuario_password " .
					" FROM usuario AS U " . 
					" WHERE U.activo = 'Y' AND U.usuario_id = " . SafeSql($iId);

			$Result = mysql_query($strCommQuery);
			$strEmail = "";
			$strPassword = "";
			if ( $ObjRs = mysql_fetch_array($Result) )
			{
				$strEmail = $ObjRs["usuario_email"];
				$strPassword = $ObjRs["usuario_password"];
				
			}
			else
			{
				$MsgBoxMessage = "No existe ningun usuario con el mail indicado.";
			}
			
			/* Liberar conjunto de resultados */
			mysql_free_result($Result);
			
			include "common/inc_database_close.php";

		}	
include "header.php";
?>
<h2>Cambiar Contraseña</h2>
<?
if($MsgBoxMessage != ""){?>
	<p style="margin-left: 40px;"><?=$MsgBoxMessage?></p>
<?
}?>
	<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" name="frmForm" onsubmit="return validarForm(this);">

	<input type="hidden" name="id" value="<?=$_SESSION["UserId"]?>">
	<input type="hidden" name="return_url" value="<?=$strReturnUrl?>" />
	<!-- FORMULARIO DE EDICION -->
	<div class="Formulario">
		<div class="separaModulo">
			<div class="AcomodaLabel">
				E-mail:
			</div>
			<input type="text" name="txtEmail" id="txtEmail" readonly="readonly" value="<?=$strEmail?>" class="AcomodaTextBox" />

		</div>
		<div class="separaModulo">		
		
			<div class="AcomodaLabel">
				Clave Actual:
			</div>
			<input type="password" name="txtPassword" id="txtPassword" value="" class="AcomodaTextBox" maxlength="20" />

		</div>
		<div class="separaModulo">		
		
			<div class="AcomodaLabel">
				Nueva Clave:
			</div>
			<input type="password" name="txtPassword2" id="txtPassword2" value="" class="AcomodaTextBox" maxlength="20" />

		</div>
		<div class="separaModulo">		
		
			<div class="AcomodaLabel">
				Repetir Nueva Clave:
			</div>
			<input type="password" name="txtPassword3" id="txtPassword3" value="" class="AcomodaTextBox" maxlength="20" />

		</div>
		<div class="PosicionBotones">
		<input class="boton" type="submit" name="btnActualizar" value="Enviar" />
		<input class="boton" type="button" value="Volver" onclick="self.location='<?=$strReturnUrl?>';" name="btnVolver" />
		</div>
	</div>

	</div>
		
	</form>
	<!-- Inicio Validaciones -->
        <script language="JavaScript" type="text/javascript">
		var frmvalidator  = new Validator("frmForm");
		frmvalidator.EnableMsgsTogether();
		frmvalidator.addValidation("txtPassword","req","Debes ingresar una clave.");
		frmvalidator.addValidation("txtPassword2","maxlen=20","La nueva clave debe tener mínimo 6 caracter y máximo 20");
		frmvalidator.addValidation("txtPassword2","minlen=6","La nueva clave debe tener mínimo 6 caracter y máximo 20.");
		frmvalidator.setAddnlValidationFunction(ChequeaNuevaPasswordMatch);
		</script>
		<!-- Fin Validaciones -->

<?


include "footer.php";
?>
