////////////////FUNCIONES AJAX///////////////////////////

var XMLHttpRequestObjectCorreccion = false;  
if (window.XMLHttpRequest) {  
	XMLHttpRequestObjectCorreccion = new XMLHttpRequest();
} else if (window.ActiveXObject) {  
	XMLHttpRequestObjectCorreccion = new ActiveXObject("Microsoft.XMLHTTP");
}  


///////////////////////////////CONVERTIR DOC///////////////////////////////
function ConvertirDoc(sArchivo){  
	if(XMLHttpRequestObjectCorreccion){
		XMLHttpRequestObjectCorreccion.open("GET", "http://localhost/conversor/Default.aspx?a=" + sArchivo, false);  
		XMLHttpRequestObjectCorreccion.onreadystatechange = ajaxRespuestaCorreccion;
		XMLHttpRequestObjectCorreccion.send(null);  
	}   
}

function ajaxRespuestaCorreccion(){  
	if (XMLHttpRequestObjectCorreccion.readyState == 4 && XMLHttpRequestObjectCorreccion.status == 200) {  
		//alert(XMLHttpRequestObjectCorreccion.responseText);  
	}  
}
/////////////////////////////FIN: CONVERTIR DOC///////////////////////////////

