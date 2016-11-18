<?
// Variable que pueden se seteadas: 
//	RequireAccess: Define el modo de Acceso. Valores posibles: [ ADMIN | LOGGED ]
//					- Descripción: ADMIN	(Permiso de Administrador)
//								   LOGGED || USER	(Solo se requiere que el usuario este loggeado)

// Si el Acceso no está definido, lo seteo para que solo valide por usuario Loggeado
if ( $RequireAccess == "" || $RequireAccess == "LOGGED" || $RequireAccess == "U" ) 
	$RequireAccess = "A|L|P|Y";
else if ( $RequireAccess == "A" ) 
	$RequireAccess = "A";
	
$vecRequireAccess = 	explode("|", $RequireAccess);

if ( !isset($_SESSION["UserLogged"]) )
{
	$_SESSION["UserLogged"] = 0;
	$_SESSION["UserId"] = 0;
	$_SESSION["UserName"] = "";
	$_SESSION["FirstName"] = "";
	$_SESSION["LastName"] = "";
	$_SESSION["Email"] = "";
	$_SESSION["UserAccess"] = "";
}

// Si el usuario no esta loggeado entonces lo mando al Login
if ( $_SESSION["UserLogged"] == 0 ) 
{
	if ( !isset($_SERVER["QUERY_STRING"]) ) {
		header("Location: index.php?return_url=" . urlencode($_SERVER["SCRIPT_NAME"]) . "?");
	}else{
		header("Location: index.php?return_url=" . urlencode($_SERVER["SCRIPT_NAME"] . "?" . $_SERVER["QUERY_STRING"]));
	}
}
else if ( in_array($_SESSION["UserAccess"], $vecRequireAccess) == false && $_SESSION["UserAccess"] != "A") 
{
	header("Location: nopermissions.php");
}
?>