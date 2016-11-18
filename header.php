<?

$MsgBoxMessageHdr = "";

// ARMO RETURN URL
$strReturnUrlHdr = $_SERVER["SCRIPT_NAME"];
if(isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] != ""){
	$strReturnUrlHdr .=  "?" . $_SERVER["QUERY_STRING"];
}
$strReturnUrlHdr = urlencode($strReturnUrlHdr);

if(GetParameter("txtUsuarioHeader") != ""){

	include ('common/inc_database_open.php');
	
	/* Realizar una consulta SQL para Verificar usuario y password */
	$strSQLComm = " SELECT * " .
				  " FROM usuario " .
				  " WHERE usuario_email = '" . SafeSql(GetParameter("txtUsuarioHeader")) . "' " .
				  " AND usuario_password = '" . md5(SafeSql(GetParameter("txtContraseniaheader"))) . "' " .
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
		
		
		//$vec = explode("@", GetParameter("txtUsuario"));
		//$strMail = $vec[0]; 
		
		$MsgBoxMessageHdr = "Bienvenido";
		$strActionLogin = "edit";
		if (GetParameter("return_url") != "" )
		{
			
			header("Location: ". GetParameter("return_url"));
			
		}
	
	}else{
		
		$MsgBoxMessageHdr = "Verifique que el usuario y clave sean válidos.";
		
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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>VirtualU - Práctica en Linea</title>
<meta http-equiv="content-type" content="text/html" charset="iso-8859-1">
<meta name="Robots" content="NOINDEX" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />

<link rel="stylesheet" type="text/css" href="css/layout.css" />
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/validator.js"></script>
<script type="text/javascript" src="js/functions.js"></script>
<script type="text/javascript" src="stmenu.js"></script>
<script type="text/javascript" src="lib/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
/*
Esta función recibe un numero y devuelve una cadena de 2 dígitos */
function dos_digitos(val){
    return (val<10)? '0'+val : String(val)
}

var xhr = false;  
if (window.XMLHttpRequest) {  
	xhr = new XMLHttpRequest();  
} else if (window.ActiveXObject) {  
	xhr = new ActiveXObject("Microsoft.XMLHTTP");  
}  

function ajaxHora(){
	xhr.open('GET','ajax_hora.php?a=' + Math.random());
	xhr.onreadystatechange = callback;
	xhr.send(null);
}

function callback(){
	if(xhr.readyState == 4){
		if(xhr.status == 200){
			var resultado = document.getElementById('dvHora');
			resultado.innerHTML = "<b>" + xhr.responseText + "</b>";
		}
	}
}

window.setInterval('ajaxHora();',20000);
</script>
</head>

<body onload="">


<!-- FIN MENU -->

	 
	<div id="outer">
	<div id="hdr" align="center"><img src="images/logo.png" align="left" style="margin-top: 5px; margin-left: 15px;"/> 

<!-- COMIENZA MENU -->
	
<div style="width: 1130px; position: relative; top: 105px;" align="right" >


<script type="text/javascript">
<!--
stm_bm(["menu79fb",970,"","blank.gif",0,"","",0,0,250,0,1000,1,0,0,"","",67108911,0,1,2,"default","hand","",1,25],this);
stm_bp("p0",[0,4,0,0,0,0,8,0,100,"",-2,"",-2,50,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"Inicio","","",-1,-1,0,"index.php","_self","","","","icon_68.gif",8,39,0,"","",0,0,0,1,1,"#FFFFF7",0,"#EDF2FF",0,"newline20.gif","bg_02[1].gif",3,3,1,1,"#B1BEF2","#B1BEF2","#000000","#0066FF","bold 9pt Verdana","bold 9pt Verdana",0,0,"","","","",0,0,0],92,39);
stm_aix("p0i1","p0i0",[0,"Mi Cuenta","","",-1,-1,0,"micuenta.php"],110,39);
stm_aix("p0i2","p0i1",[0,"Mis Materias","","",-1,-1,0,"mismaterias.php"],110,39);
stm_aix("p0i3","p0i2",[0,"Mis TPs","","",-1,-1,0,"mistps.php"],100,39);
stm_aix("p0i4","p0i2",[0,"Mis Mensajes","","",-1,-1,0,"mismensajes.php"],110,39);
stm_aix("p0i5","p0i2",[0,"Mi Calendario","","",-1,-1,0,"micalendario.php"],120,39);
stm_aix("p0i6","p0i1",[0,"Salas de Chat","","",-1,-1,0,"salasdechat.php"],120,39);
stm_aix("p0i7","p0i1",[0,"Universidades","","",-1,-1,0,"universidades.php"],120,39);
stm_aix("p0i8","p0i1",[0,"Contacto","","",-1,-1,0,"contacto.php"],100,39);
stm_ep();
stm_em();
//-->
</script>

</div>
            </div>

<div id="bodyblock" align="right">
<div id="l-col" align="center">
		<div class="slogan"><b>Pr&aacute;ctica en L&iacute;nea</b></div>
<div style="position:relative; height: 116px;">
<img src="images/learning.jpg" alt="VirtualU" style="width: 155px; border: 1px groove #566D7E; border-right: none;" align="left" />
</div>
<h4 align="center" class="tituloIzq">Login</h4>

   <p align="center"> <b style="color: blue; font: 11px Verdana, Arial"> <?=$MsgBoxMessageHdr?></b></p>

   <?

if (isset($_SESSION["UserLogged"]) && $_SESSION["UserLogged"] == 1){ ?>
<p align="center"><b> <?=$_SESSION["FirstName"]?> <?=$_SESSION["LastName"]?>  </b><p>
<a href="micuenta.php"> Mi Cuenta</a><br/>
<a href="cambiarcontrasenia.php?return_url=<?=$strReturnUrlHdr?>"> Cambiar Contrase&ntilde;a</a><br/>
<a href="logout.php"> Cerrar Sesi&oacute;n</a>
<?
}else{
?>
		<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post">
			<input type="hidden" name="return_url" value="<?=GetParameter("return_url")?>" />
			<p style="margin-top: -10px;"><label for="txtUsuarioHeader">Email: </label><br/><input type="text" name="txtUsuarioHeader" id="txtUsuarioHeader" class="login" /></p>
			<p style="margin-top: -10px;"><label for="txtContraseniaheader">Contrase&ntilde;a: </label><br/>
			<input type="password" name="txtContraseniaheader" id="txtContraseniaheader" class="login"/></p>
			<input type="submit" id="submitloginformhdr" name="submitloginformhdr"  class="aceptarbtn" value="Aceptar" /> 
		</form>
		
		<div style="height: 5px; "></div>
	<a href="olvidecontrasenia.php" >Olvide mi Contrase&ntilde;a</a><br/>
	<a href="micuenta.php" style="line-height: 30px; margin-bottom: 10px;">Registrarse</a>

<? } ?>
<br/><br/>
<h4 align="center" class="tituloIzq">Hora Oficial</h4>

<br/><div id="dvHora"><b><?=date("H:i");?></b></div><br/><br/>
<br/>
<h4 align="center" class="tituloIzq">Links</h4>
<a href="http://www.iali-ingles.com.ar/" target="_blank" title="Estudiá Inglés en Zona Oeste">Estudiá Inglés</a><br />
<!-- <a href="#">Link 2</a><br />
<a href="#">Link 3</a><br />
<a href="#">Link 4</a><br /> -->

</div>
<div id="cont">