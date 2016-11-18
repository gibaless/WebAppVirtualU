<?
function funcLoguearUsuario($strUsuario = '',$strPassword = '',  $link = ''){
	$Logueado = false;
	//$strAccess = (isset($_REQUEST["access"]) ? $_REQUEST["access"] : '');
	//if ($strAccess == "") 
		$strAccess = "U";
	
	if($link == ''){
		include "common/inc_database_open.php";
	}
				
	/* Realizar una consulta SQL para Verificar usuario y password */
	$strSQLComm = " SELECT * " .
				  " FROM users " .
				  " WHERE username = '" . SafeSql($strUsuario) . "' " .
				  " AND password = '" . SafeSql($strPassword) . "' " .
				  " AND access IN ('" . $strAccess . "','A') " . 
				  " AND active = 'Y' ";
	$Result = mysql_query($strSQLComm) or die("Error en consulta: " . mysql_error());	
	if($oRs = mysql_fetch_array($Result))
	{
		//session_start();
		$_SESSION["UserLogged"] = 1;
		$_SESSION["UserId"] = $oRs["user_id"];
		$_SESSION["UserName"] = $oRs["username"];
		$_SESSION["FirstName"] = $oRs["nombre"];
		$_SESSION["LastName"] = $oRs["apellido"];
		$_SESSION["Email"] = $oRs["email"];
		$_SESSION["UserAccess"] = $oRs["access"];
					
		/* Liberar conjunto de resultados */
		mysql_free_result($Result);
		
		if($link == '')
			include "common/inc_database_close.php";
		$Logueado = true;
	}
	return($Logueado);
}
?>

