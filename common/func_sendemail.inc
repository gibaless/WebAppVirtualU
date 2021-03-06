<?
/**********************************************************************
// ** FUNCTION **
// Nombre: func_sendemail.inc
//----------------------------------------------------------------------
// Autor: Lucas F. Ruano
***********************************************************************/

function SendEmail($FromName, $FromEmail, $ToEmail, $Subject, $Message, $ReplyTo = '', $RecipientCC = '', $RecipientBCC = '')
{
	
	//para el env�o en formato HTML
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	
	//direcci�n del remitente
	$headers .= ($FromName != "" ? "From: $FromName " : '') . "<$FromEmail>\r\n";
	
	if($ReplyTo != ""){
		//direcci�n de respuesta, si queremos que sea distinta que la del remitente.
		$headers .= "Reply-To: $ReplyTo\r\n";
	}
	
	
	//ruta del mensaje desde origen a destino
	//$headers .= "Return-path: holahola@desarrolloweb.com\r\n";
	
	if($RecipientCC != ""){
		//direcciones que recibi�n copia
		$headers .= "Cc: $RecipientCC\r\n";
	}
	
	if($RecipientBCC != ""){
		//direcciones que recibir�n copia oculta
		$headers .= "Bcc: $RecipientBCC\r\n"; 
	}
		
		
	return mail($ToEmail,$Subject,$Message,$headers);
}
?>