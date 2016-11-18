////////////////FUNCIONES AJAX///////////////////////////
var XMLHttpRequestObjectFacultades = false; 
var XMLHttpRequestObjectCarreras = false;  
var XMLHttpRequestObjectMaterias = false;  
var XMLHttpRequestObjectComisiones = false;  
var XMLHttpRequestObjectGrupos = false;  
var XMLHttpRequestObjectUsuarios = false;
var XMLHttpRequestObjectUsuariosComision = false;
if (window.XMLHttpRequest) {  
	XMLHttpRequestObjectFacultades = new XMLHttpRequest();  
	XMLHttpRequestObjectCarreras = new XMLHttpRequest();  
	XMLHttpRequestObjectMaterias = new XMLHttpRequest();  
	XMLHttpRequestObjectComisiones = new XMLHttpRequest(); 
	XMLHttpRequestObjectGrupos = new XMLHttpRequest();  
	XMLHttpRequestObjectUsuarios = new XMLHttpRequest();
	XMLHttpRequestObjectUsuariosComision = new XMLHttpRequest();
} else if (window.ActiveXObject) {  
	XMLHttpRequestObjectFacultades = new ActiveXObject("Microsoft.XMLHTTP");  
	XMLHttpRequestObjectCarreras = new ActiveXObject("Microsoft.XMLHTTP");  
	XMLHttpRequestObjectMaterias = new ActiveXObject("Microsoft.XMLHTTP");
	XMLHttpRequestObjectComisiones = new ActiveXObject("Microsoft.XMLHTTP"); 
	XMLHttpRequestObjectGrupos = new ActiveXObject("Microsoft.XMLHTTP");
	XMLHttpRequestObjectUsuarios = new ActiveXObject("Microsoft.XMLHTTP");
	XMLHttpRequestObjectUsuariosComision = new ActiveXObject("Microsoft.XMLHTTP");
}  

function select_innerHTML(objeto,innerHTML){
    objeto.innerHTML = ""
    var selTemp = document.createElement("micoxselect")
    var opt;
    selTemp.id="micoxselect1"
    document.body.appendChild(selTemp)
    selTemp = document.getElementById("micoxselect1")
    selTemp.style.display="none"
    if(innerHTML.indexOf("<option")<0){//se não é option eu converto
        innerHTML = "<option>" + innerHTML + "</option>"
    }
    innerHTML = innerHTML.replace(/<option/g,"<span").replace(/<\/option/g,"</span")
    selTemp.innerHTML = innerHTML
      
    
    for(var i=0;i<selTemp.childNodes.length;i++){
  var spantemp = selTemp.childNodes[i];
  
        if(spantemp.tagName){     
            opt = document.createElement("OPTION")
    
   if(document.all){ //IE
    objeto.add(opt)
   }else{
    objeto.appendChild(opt)
   }       
    
   //getting attributes
   for(var j=0; j<spantemp.attributes.length ; j++){
    var attrName = spantemp.attributes[j].nodeName;
    var attrVal = spantemp.attributes[j].nodeValue;
    if(attrVal){
     try{
      opt.setAttribute(attrName,attrVal);
      opt.setAttributeNode(spantemp.attributes[j].cloneNode(true));
     }catch(e){}
    }
   }
   //getting styles
   if(spantemp.style){
    for(var y in spantemp.style){
     try{opt.style[y] = spantemp.style[y];}catch(e){}
    }
   }
   //value and text
   opt.value = spantemp.getAttribute("value")
   opt.text = spantemp.innerHTML
   //IE
   opt.selected = spantemp.getAttribute('selected');
   opt.className = spantemp.className;
  } 
 }    
 document.body.removeChild(selTemp)
 selTemp = null
}

///////////////////////////////OBTENER FACULTAD///////////////////////////////
function TraerFacultades(iIdFacultad, sDefecto, sUsuario){  
	if(XMLHttpRequestObjectFacultades){
		var iIdUniversidad = document.getElementById("cmbUniversidad").value; 
		var oCarrera = document.getElementById("cmbCarrera"); 
		var oMateria = document.getElementById("cmbMateria"); 
		var oComision = document.getElementById("cmbComision"); 
		var oGrupo = document.getElementById("cmbGrupo"); 
		var oUsuario = document.getElementById("cmbUsuario"); 
		
		if(oCarrera != null){
			ResetearCarreras(sDefecto);
		}
		if(oMateria != null){
			ResetearMaterias(sDefecto);
		}
		if(oComision != null){
			ResetearComisiones(sDefecto);
		}
		if(oGrupo != null){
			ResetearGrupos(sDefecto);
		}
		if(oUsuario != null){
			ResetearUsuarios(sDefecto);
		}
		
		if(iIdUniversidad == ""){
			iIdUniversidad = "0";
		}
		XMLHttpRequestObjectFacultades.open("GET", "ajax_facultades.php?idu=" + iIdUniversidad + "&id=" + iIdFacultad + "&d=" + sDefecto + "&u=" + sUsuario);  
		XMLHttpRequestObjectFacultades.onreadystatechange = ajaxRespuestaFacultades;
		XMLHttpRequestObjectFacultades.send(null);  
	}   
}

function ajaxRespuestaFacultades(){  
	if (XMLHttpRequestObjectFacultades.readyState == 4 && XMLHttpRequestObjectFacultades.status == 200) {  
		select_innerHTML(document.getElementById("cmbFacultad"), XMLHttpRequestObjectFacultades.responseText);  
	}  
}
/////////////////////////////FIN: OBTENER FACULTAD///////////////////////////////
///////////////////////////////OBTENER CARRERAS///////////////////////////////
function TraerCarreras(iIdCarrera, sDefecto, sUsuario){  
	if(XMLHttpRequestObjectCarreras){
		var iIdFacultad = document.getElementById("cmbFacultad").value; 
		var oMateria = document.getElementById("cmbMateria"); 
		var oComision = document.getElementById("cmbComision"); 
		var oGrupo = document.getElementById("cmbGrupo"); 
		var oUsuario = document.getElementById("cmbUsuario"); 
		
		if(oMateria != null){
			ResetearMaterias(sDefecto);
		}
		if(oComision != null){
			ResetearComisiones(sDefecto);
		}
		if(oGrupo != null){
			ResetearGrupos(sDefecto);
		}
		if(oUsuario != null){
			ResetearUsuarios(sDefecto);
		}
		
		if(iIdFacultad == ""){
			iIdFacultad = "0";
		}
		XMLHttpRequestObjectCarreras.open("GET", "ajax_carreras.php?idf=" + iIdFacultad + "&id=" + iIdCarrera + "&d=" + sDefecto + "&u=" + sUsuario);  
		XMLHttpRequestObjectCarreras.onreadystatechange = ajaxRespuestaCarreras;
		XMLHttpRequestObjectCarreras.send(null);  
	}   
}

function ajaxRespuestaCarreras(){  
	if (XMLHttpRequestObjectCarreras.readyState == 4 && XMLHttpRequestObjectCarreras.status == 200) {  
		select_innerHTML(document.getElementById("cmbCarrera"), XMLHttpRequestObjectCarreras.responseText);  
	}  
}
/////////////////////////////FIN: OBTENER CARRERAS///////////////////////////////
///////////////////////////////OBTENER MATERIAS///////////////////////////////
function TraerMaterias(iIdMateria, sDefecto, sUsuario){  
	if(XMLHttpRequestObjectMaterias){
		var iIdCarrera = document.getElementById("cmbCarrera").value; 
		var oComision = document.getElementById("cmbComision"); 
		var oGrupo = document.getElementById("cmbGrupo"); 
		var oUsuario = document.getElementById("cmbUsuario"); 
		
		if(oComision != null){
			ResetearComisiones(sDefecto);
		}
		if(oGrupo != null){
			ResetearGrupos(sDefecto);
		}
		if(oUsuario != null){
			ResetearUsuarios(sDefecto);
		}
		
		if(iIdCarrera == ""){
			iIdCarrera = "0";
		}
		
		XMLHttpRequestObjectMaterias.open("GET", "ajax_materias.php?idr=" + iIdCarrera + "&id=" + iIdMateria + "&d=" + sDefecto + "&u=" + sUsuario);  
		XMLHttpRequestObjectMaterias.onreadystatechange = ajaxRespuestaMaterias;
		XMLHttpRequestObjectMaterias.send(null);  
	}   
}

function ajaxRespuestaMaterias(){  
	if (XMLHttpRequestObjectMaterias.readyState == 4 && XMLHttpRequestObjectMaterias.status == 200) {  
		select_innerHTML(document.getElementById("cmbMateria"), XMLHttpRequestObjectMaterias.responseText);  
	}  
}
/////////////////////////////FIN: OBTENER MATERIAS///////////////////////////////
///////////////////////////////OBTENER COMISIONES///////////////////////////////
function TraerComisiones(iIdComision, sDefecto, sUsuario){  
	if(XMLHttpRequestObjectComisiones){
		
		var iIdMateria = document.getElementById("cmbMateria").value; 
		var oGrupo = document.getElementById("cmbGrupo"); 
		var oUsuario = document.getElementById("cmbUsuario"); 
		
		if(oGrupo != null){
			ResetearGrupos(sDefecto);
		}
		if(oUsuario != null){
			ResetearUsuarios(sDefecto);
		}
		
		if(iIdMateria == ""){
			iIdMateria = "0";
		}
		
		XMLHttpRequestObjectComisiones.open("GET", "ajax_comisiones.php?idm=" + iIdMateria + "&id=" + iIdComision  + "&d=" + sDefecto + "&u=" + sUsuario);  
		XMLHttpRequestObjectComisiones.onreadystatechange = ajaxRespuestaComisiones;
		XMLHttpRequestObjectComisiones.send(null);  
	}   

}

function ajaxRespuestaComisiones(){  
	if (XMLHttpRequestObjectComisiones.readyState == 4 && XMLHttpRequestObjectComisiones.status == 200) {  
		select_innerHTML(document.getElementById("cmbComision"), XMLHttpRequestObjectComisiones.responseText);  
	}  
}
/////////////////////////////FIN: OBTENER COMISIONES///////////////////////////////
///////////////////////////////OBTENER GRUPOS///////////////////////////////
function TraerGrupos(iIdGrupo, sDefecto, sUsuario){  
	if(XMLHttpRequestObjectGrupos){
		var iIdComision = document.getElementById("cmbComision").value; 
		if(iIdComision == ""){
			iIdComision = "0";
		}
		
		XMLHttpRequestObjectGrupos.open("GET", "ajax_grupos.php?idc=" + iIdComision + "&id=" + iIdGrupo + "&d=" + sDefecto + "&u=" + sUsuario);  
		XMLHttpRequestObjectGrupos.onreadystatechange = ajaxRespuestaGrupos;
		XMLHttpRequestObjectGrupos.send(null);  
	}   

}

function ajaxRespuestaGrupos(){  
	if (XMLHttpRequestObjectGrupos.readyState == 4 && XMLHttpRequestObjectGrupos.status == 200) {  
		select_innerHTML(document.getElementById("cmbGrupo"), XMLHttpRequestObjectGrupos.responseText);  
	}  
}
/////////////////////////////FIN: OBTENER GRUPOS///////////////////////////////
///////////////////////////////OBTENER USUARIOS///////////////////////////////
function TraerUsuarios(iIdUsuario, sDefecto, sUsuario){  
	if(XMLHttpRequestObjectUsuarios){
		var iIdComision = document.getElementById("cmbComision").value; 
		var iIdGrupo = document.getElementById("cmbGrupo").value; 
		if(iIdGrupo == ""){
			XMLHttpRequestObjectUsuarios.open("GET", "ajax_usuarios_comision.php?idc=" + iIdComision + "&id=" + iIdUsuario + "&d=" + sDefecto + "&u=" + sUsuario); 
		}else{
			XMLHttpRequestObjectUsuarios.open("GET", "ajax_usuarios.php?idg=" + iIdGrupo + "&id=" + iIdUsuario + "&d=" + sDefecto + "&u=" + sUsuario);	
		}
		
		XMLHttpRequestObjectUsuarios.onreadystatechange = ajaxRespuestaUsuarios;
		XMLHttpRequestObjectUsuarios.send(null);  
	}   

}

function ajaxRespuestaUsuarios(){  
	if (XMLHttpRequestObjectUsuarios.readyState == 4 && XMLHttpRequestObjectUsuarios.status == 200) {  
		select_innerHTML(document.getElementById("cmbUsuario"), XMLHttpRequestObjectUsuarios.responseText);  
	}  
}
/////////////////////////////FIN: OBTENER USUARIOS///////////////////////////////
///////////////////////////////OBTENER USUARIOS POR COMISIÓN///////////////////////////////
function TraerUsuariosComision(iIdUsuario, sDefecto, sUsuario){  
	if(XMLHttpRequestObjectUsuariosComision){
		var iIdComision = document.getElementById("cmbComision").value; 
		if(iIdComision == ""){
			iIdComision = "";
		}
		
		XMLHttpRequestObjectUsuariosComision.open("GET", "ajax_usuarios_comision.php?idc=" + iIdComision + "&id=" + iIdUsuario + "&d=" + sDefecto + "&u=" + sUsuario);  
		XMLHttpRequestObjectUsuariosComision.onreadystatechange = ajaxRespuestaUsuariosComision;
		XMLHttpRequestObjectUsuariosComision.send(null);  
	}   

}

function ajaxRespuestaUsuariosComision(){  
	if (XMLHttpRequestObjectUsuariosComision.readyState == 4 && XMLHttpRequestObjectUsuariosComision.status == 200) {  
		select_innerHTML(document.getElementById("cmbUsuario"), XMLHttpRequestObjectUsuariosComision.responseText);  
	}  
}
/////////////////////////////FIN: OBTENER USUARIOS POR COMISIÓN///////////////////////////////
////////////////////////FIN:FUNCIONES AJAX///////////////////////////

function ResetearFacultades(sDefecto){
	select_innerHTML(document.getElementById("cmbFacultad"), "<option value=''>" + sDefecto + "</option>");
}
function ResetearCarreras(sDefecto){
	select_innerHTML(document.getElementById("cmbCarrera"), "<option value=''>" + sDefecto + "</option>");
}
function ResetearMaterias(sDefecto){
	select_innerHTML(document.getElementById("cmbMateria"), "<option value=''>" + sDefecto + "</option>");
}
function ResetearComisiones(sDefecto){
	select_innerHTML(document.getElementById("cmbComision"), "<option value=''>" + sDefecto + "</option>");
}
function ResetearGrupos(sDefecto){
	if(sDefecto == "--Todas--"){
		sDefecto = "--Todos--";
	}
	select_innerHTML(document.getElementById("cmbGrupo"), "<option value=''>" + sDefecto + "</option>");
}
function ResetearUsuarios(sDefecto){
	if(sDefecto == "--Todas--"){
		sDefecto = "--Todos--";
	}
	select_innerHTML(document.getElementById("cmbUsuario"), "<option value=''>" + sDefecto + "</option>");
}

function CerrarPopup(){
	window.opener.location.reload();
	window.close();	
}

function ConfirmDelete(url, message) {
	var bResult;
	bResult = confirm(message);
	if ( bResult )
		window.location.href = url;
}