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


include "header.php";
?>
<h3>Contactenos</h3>

<?php

$nombre = $_POST['nombre'];
$mail = $_POST['mail'];
$subject = $_POST['titulo'];

$header = 'From: ' . $mail . " \r\n";
$header .= "X-Mailer: PHP/" . phpversion() . " \r\n";
$header .= "Mime-Version: 1.0 \r\n";
$header .= "Content-Type: text/plain";

$mensaje = "Este mensaje fue enviado por: " . $nombre . " \r\n\n";
$mensaje .= " Su e-mail es: " . $mail . " \r\n\n";
$mensaje .= " Mensaje: " . $_POST['mensaje'] . " \r\n\n";
$mensaje .= "Enviado el " . date('d/m/Y', time());

$para = 'gibaless@gmail.com';
$asunto = '** Contacto desde VirtualU.com.ar: ' . $subject;

?>
<div style="margin: 20px;">
<b>
<?

if (mail($para, $asunto, utf8_decode($mensaje), $header))
{
  ?> <p>Mensaje enviado exitosamente</p>

<?
} else {?>
  <p>Se ha producido un error en el envio del mensaje. <br/>Vuelva a intentarlo.</p>

<? }
?>

</b>

</div>
<? 
include "common/inc_database_close.php";
include "footer.php"; ?>
