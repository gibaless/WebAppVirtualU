<?
session_start();
header("Buffer: true"); //Mando todo junto (todo el Buffer)
header("cache-control: Private"); //Evito el proxy por HTML
header("pragma: no-cache"); //Evito la cache del navegador por HTML
//header("ExpiresAbsolute: " . time() -10); //Expira página Absolutamente
header("Expires: -100000"); //Expira página

include "includes/sitesettings_inc.php";
include "includes/functions_inc.php"; // 
include "common/func_getparameter.inc"; // AGREGO EL GETPARAMETER.
include "header.php";
?>
	<h3>Advertencia</h3>
	<p style="color: red; text-align: center;"> Usted no tiene los permisos necesarios para ver la página. </p>
	
<?
  include "footer.php";
 ?>