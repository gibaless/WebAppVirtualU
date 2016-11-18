<?
session_start();


/* INCLUYO ARCHIVO DE CONFIGURACIÃ“N. */
include "includes/functions_inc.php"; // AGREGO EL ARCHIVO DE FUNCIONES.
include "common/func_getparameter.inc"; // AGREGO EL GETPARAMETER.


header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
header("Expires: -100000"); //Expira pÃ¡gina

$MsgBoxMessage = "";

if(GetParameter("txtUsuario") != ""){
	// Busco el usuario para loggearlo
	$strAccess = GetParameter("access");
	//if ($strAccess == "") 
		//$strAccess = "U";
	
	include "common/inc_database_open.php";
	
	/* Realizar una consulta SQL para Verificar usuario y password */
	$strSQLComm = " SELECT * " .
				  " FROM usuario " .
				  " WHERE usuario_email = '" . SafeSql(GetParameter("txtUsuario")) . "' " .
				  " AND usuario_password = '" . md5(SafeSql(GetParameter("contrasenia"))) . "' " .
				  " AND activo = 'Y' ";
	$Result = mysql_query($strSQLComm) or die("Error en consulta: " . mysql_error());	
	if($oRs = mysql_fetch_array($Result))
	{
		//session_start();
		
		$_SESSION["UserLogged"] = 1;
		$_SESSION["UserId"] = $oRs["usuario_id"];
		$_SESSION["UserName"] = $oRs["usuario_email"];
		$_SESSION["FirstName"] = $oRs["usuario_nombre"];
		$_SESSION["LastName"] = $oRs["usuario_apellido"];
		$_SESSION["UserAccess"] = $oRs["tipo_usuario"];
		
		/* Liberar conjunto de resultados */
		mysql_free_result($Result);
		
		//$vec = explode("@", GetParameter("txtUsuario"));
		//$strMail = $vec[0]; 

		//if(GetParameter("contrasenia") == $strMail){
		//	header("Location: cambiar_clave.php?return_url=". GetParameter("return_url"));
		// }else{
		
			if (GetParameter("return_url") != "" )
				{
					
					header("Location: ". GetParameter("return_url"));
					
				}
			else{
					header("Location: index.php");
				}
		// }
		exit();
	}else{
		
		$MsgBoxMessage = "Verifique que el usuario y clave sean vÃ¡lidos.";
		
	}
	
	/* Liberar conjunto de resultados */
	mysql_free_result($Result);
	include "common/inc_database_close.php";
}

$strUsuario = "";
if(GetParameter("txtUsuario") != ""){
	$strUsuario = GetParameter("txtUsuario");
}
$strClave = "";
if(GetParameter("contrasenia") != ""){
	$strClave = GetParameter("contrasenia");
}

include "header.php"; 
?>
<!-- Bienvenida -->
	<div style="float: left; width: 604px; height: 120px; padding: 5px;">
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="600" height="120" id="Bienvenido.swf" align="middle">
	<param name="allowScriptAccess" value="sameDomain">
	<param name="movie" value="Bienvenido.swf">
	<param name="quality" value="high">
	<param name="bgcolor" value="#ffffff">
	<param name="devicefont" value="true">
	<embed src="Bienvenido.swf" quality="high" bgcolor="#ffffff" width="600" height="120" name="Bienvenido.swf" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
	</object>
	</div>
<!-- FIN DE BIENVENIDA -->

<div style="float: right; width: 374px; height: 128px;">
	<span style="position: relative; font: 20px Tahoma, Arial; font-weight: bold; top: 60px; margin-left: 45px;">Accede al Chat ></span>
	<a href="salasdechat.php"><img src="./images/sala_chat.jpg" align="right" style="border:none; margin-top: 10px; margin-right: 20px;" /></a>
</div>
<div style="float: right; width: 374px; height: 300px;">
	
	<p style="margin-bottom: -25px;">&nbsp;</p>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="360" height="200" id="home.swf" align="middle">
	<param name="allowScriptAccess" value="sameDomain">
	<param name="movie" value="home.swf">
	<param name="quality" value="high">
	<param name="bgcolor" value="#ffffff">
	<param name="devicefont" value="true">
	<embed src="home.swf" quality="high" bgcolor="#ffffff" width="360" height="200" name="home.swf" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
	</object>
</div>


<div style="float: left; border-top:2px groove #0066CC; background-color: #EEEFF9; margin-left: 5px;width: 600px; height: 296px; font: 14px Tahoma, Verdana, Arial; ">

<p align="center"> <b style="color: blue;"> <?=$MsgBoxMessage?></b></p>
<? 
if (isset($_SESSION["UserLogged"]) && $_SESSION["UserLogged"]){
?>
		<p align="center" ><br/><br/><b>¡¡ Bienvenido a VirtualU !!</b> <br/><br/> Ya podés comenzar a utilizar todas las ventajas de este sitio web. <br/><br/>
	Comienza navegando por el menú principal donde vas a encontrar toda la guía<br/> necesaria para poder ir acomodandote.</p>

<?
}else{
?>

		<span style="position: relative; top: 5px; margin-left: 30px;">Registrate al sistema, es gratis, simple y r&aacute;pido!</span>
		<center>
		<div style="position: relative; width: 220px; 
					top: 30px; padding: 5px;  font-size: 24px;
					 text-align: center; padding: 10px;
					text-shadow: #cccccc 1px 1px 1px;">
					<a href="micuenta.php"style="font: 14px Verdana, Calibri, Arial;">QUIERO REGISTRARME</a>
		</div>
		</center>
		<span style="position: relative; top: 55px; margin-left: 30px;">Si ya sos usuario, inicia sesi&oacute;n aqui:
		<br/>
		</span>
	
		

		<div style="position: relative; top: 60px; left: 80px; width: 260px; font: 18px Tahoma, Calibri, Verdana, Arial; font-weight: bold;  
			padding: 10px  20px; ">
   		
		<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post" >
		<span style="float: left;  ">
		<input type="hidden" name="return_url" value="<?=GetParameter("return_url")?>" />
		<input type="hidden" name="return_url" value="<?=$_REQUEST["return_url"]?>">
		<input type="hidden" name="access" value="<?=$_REQUEST["access"]?>">
				
        	<span><label for="txtUsuario">Email: </label><br/><input type="text" name="txtUsuario" id="txtUsuario" class="login" style="width: 180px;" /></span>
		   <br/>
		<span><label for="contrasenia">Contrase&ntilde;a: </label><br/><input type="password" name="contrasenia" id="contrasenia" class="login" style="width: 180px;"/>
        	     </span>
		</span>
		<span style="float: right; width: 60px; ">
			<br/><br/>
			<input type="image" src="./images/ok.png" id="submitloginform" name="submitloginform"  alt="Aceptar" style="border:none; width: 35px;"/> 
		</span>
   		 </form>
		
		</div>
		
	
<?}?>

</div>




<? include "footer.php"; ?>
