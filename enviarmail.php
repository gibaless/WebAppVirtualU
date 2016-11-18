<script language="javascript" type="text/javascript">
////////////////FUNCIONES AJAX///////////////////////////

var XMLHttpRequestObjectMail = false;  
if (window.XMLHttpRequest) {  
	XMLHttpRequestObjectMail = new XMLHttpRequest();
} else if (window.ActiveXObject) {  
	XMLHttpRequestObjectMail = new ActiveXObject("Microsoft.XMLHTTP");
}  


///////////////////////////////ENVIAR MAIL///////////////////////////////
function EnviarMail(sNombre, sMail, sTitulo, sMensaje){  
	if(XMLHttpRequestObjectMail){
		XMLHttpRequestObjectMail.open("POST", "http://www.gibaless.com.ar/virtualuphp/envia_nota.php", false);  
		XMLHttpRequestObjectMail.setRequestHeader('Content-type','application/x-www-form-urlencoded');
		XMLHttpRequestObjectMail.onreadystatechange = ajaxRespuestaMail;
		XMLHttpRequestObjectMail.send("n=" + sNombre + "&m=" + sMail + "&s=" + sTitulo + "&b=" + sMensaje);  
	}   
}

function ajaxRespuestaMail(){  
	if (XMLHttpRequestObjectMail.readyState == 4 && XMLHttpRequestObjectMail.status == 200) {  
		//alert(XMLHttpRequestObjectMail.responseText);  
	}  
}
/////////////////////////////FIN: ENVIAR MAIL///////////////////////////////
</script>