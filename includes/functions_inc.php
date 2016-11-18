<?
///////////////////////////////////////////////////
/////////     ARCHIVO DE FUNCIONES		  /////////
///////////////////////////////////////////////////

// Función la cual a una url le agregar el urlencode.
function url_encode ($str) { 
    return htmlentities(urlencode($str)); 
}

// Función la cual reemplaza la comilla simple por la doble comilla simple.
function SafeSql($text){
	return stripslashes(str_replace("'", "''", $text));
}

// Función la cual reemplaza la barra invertida que aparece al salvar un string.
function ViewText($text){
	return stripslashes($text);
}

// Devuelve el mismo texto en mayúscula.
function UCase($text){
	return strtoupper($text);
}

// Reemplaza el char 13 por <br>
function ConvertChrToBr($text){
	return stripslashes(str_replace(chr(13), "<br />", $text));
}

// Calcula la edad (formato: mes/dia/año)
function edad($edad)
{
	static $mes = 0;
	static $dia = 0;
	static $anio = 0;
	static $dia_dif = 0;
	static $mes_dif = 0;
	static $anio_dif = 0;
	
	list($mes,$dia,$anio) = explode("/",$edad);
	$anio_dif = date("Y") - $anio;
	$mes_dif = date("m") - $mes;
	$dia_dif = date("d") - $dia;
	
	if ($dia_dif < 0 && $mes_dif == 0)
	{
		$anio_dif--;
	// }elseif($mes_dif < 0){
	}
	
	return $anio_dif;
}

// Funcion la cual guarda una imagen.
function GuardarImagen($uploadedfile, $filename, $newwidth, $newheight, $radio = false, $newquality = 100)
{
	ini_set('memory_limit','128M'); // Añade esta línea
	ini_set('upload_max_filesize','10M'); // Añade esta línea
	
	// Create an Image from it so we can do the resize
	$src = imagecreatefromjpeg($uploadedfile);
	
	// Capture the original size of the uploaded image
	list($width,$height)=getimagesize($uploadedfile);
	
	// For our purposes, I have resized the image to be
	// 600 pixels wide, and maintain the original aspect
	// ratio. This prevents the image from being "stretched"
	// or "squashed". If you prefer some max width other than
	// 600, simply change the $newwidth variable
	
	if($radio == true && 1==2)
	{
		$heightAux = $newheight;
		$newheight=($height/$width)*$newwidth;
		
		if($newheight > $heightAux)
			$newheight = $heightAux;
	}
	
	//---------------------
	//define size for new image
	if ($newwidth == 0)
		$newwidth = $width;
	if ($newheight == 0)
		$newheight = $height;
	
	if ($radio)
	{
		$intNewWidth = 0;
		$intNewHeight = 0;
		
		if ($newwidth < $width || $newheight < $height)
		{
			if ($newwidth >= $newheight)
			{
				$intNewWidth = (int)((float)$newheight * ((float)$width / (float)$height));
				$intNewHeight = $newheight;
			}
			else
			{
				$intNewWidth = $newwidth;
				$intNewHeight = (int)((float)$newwidth * ((float)$height / (float)$width));
			}
			
			if ($intNewWidth > $newwidth)
			{
				$intNewWidth = $newwidth;
				$intNewHeight = (int)((float)$newwidth * ((float)$height / (float)$width));
			}
			if ($intNewHeight > $newheight)
			{
				$intNewWidth = (int)((float)$newheight * ((float)$width / (float)$height));
				$intNewHeight = $newheight;
			}
		}
		else
		{
			$intNewWidth = $width;
			$intNewHeight = $height;
		}
		
		$newwidth = $intNewWidth;
		$newheight = $intNewHeight;
	}
	//---------------------
	
	
	
	$tmp=imagecreatetruecolor($newwidth,$newheight);
	
	// this line actually does the image resizing, copying from the original
	// image into the $tmp image
	imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
	
	// now write the resized image to disk. I have assumed that you want the
	// resized, uploaded image file to reside in the ./images subdirectory.
	//$filename = "../imagenes/imagebank/prueba/". $_FILES['txtImagen']['name'];
	imagejpeg($tmp,$filename,$newquality);
	
	
	/// after resizing is complete, put memory limit back. 
	ini_restore ( 'upload_max_filesize' );
	ini_restore ( 'memory_limit' ); 

	
	imagedestroy($src);
	imagedestroy($tmp); // NOTE: PHP will clean up the temp file it created when the request
	// has completed.
	/*********** FIN: IMAGEN ****************/
}


// Función la cual calcula un algoritmo.
function CalcularMap($Id, $text){
	//return md5($text);
	return $Id . bin2hex($text);
}


// Devuelve el estado del usuario.
function ObtenerNomEstadoUsuario($Active){
	$strResult = "";
	
	switch($Active){
		case "Y":
			$strResult = "Activo";
			break;
		case "N":
			$strResult = "Inactivo";
			break;
	}
	
	return $strResult;
}

function MostrarNoData(){
	?><p>&nbsp;</p><p>&nbsp;</p><p align="center"><b>There are no available results.</b></p><p>&nbsp;</p><p>&nbsp;</p><?
}

function GetFileSize($path)
{
	return filesize($path);
}

function GetFileWidthHeight($path)
{
	list($width, $height) = getimagesize($path);
	return $width .'x'. $height;
}


function FechaActual($hora = 'N', $option = '', $formato = '')
{
	$Date = "";
	switch($option)
	{
	   case 1:
		  	if($hora == 'Y')
			{
				$Date = date("Y/m/d H:i:s", time());
			}else{
				$Date = date("Y/m/d", time());
			}
		  break;
	   case 2:
		  	if($formato != '')
			{
				$Date = date($formato, time());
			}else if($hora == 'Y'){
				$Date = date("d/m/Y H:i:s", time());
			}else{
				$Date = date("d/m/Y", time());
			}
		  break;
	   default:
			if($hora == 'Y')
			{
				$Date = date("d/m/Y H:i:s", time());
			}else{
				$Date = date("d/m/Y", time());
			}
		  break;
	
	}

	return $Date;
}


// Funcion la cual borra un ARCHIVO.
function BorrarArchivo($path_completo)
{
	$bSuccess = false;
	if(file_exists($path_completo))
	{
		$bSuccess = true;
		unlink($path_completo);
	}
	return $bSuccess;
}

// Funcion la cual devuelve el resultado de un UPLOAD de ARCHIVO.
function ObtenerNomArchivoDB($campo, $upload, $chk, $Path)
{
	static $strResult = "";
	
	if((trim("".$chk) != "") && (trim("".$chk) != trim("".$upload)))
	{
		BorrarArchivo($Path . $chk);
		
		if(trim("".$upload) != "")
		{
			$strResult = $upload;
		}
	}else if(trim("".$campo) != ""){
		$strResult = $campo;
	}
	
	return $strResult;
}


// Funcion la cual valida si la url tiene el http sino se lo agrega.
function VerificarHTTP($url)
{
	if(strlen($url) > 6 && strtolower(substr($url, 0, 4)) != "http")
		$url = "http://" . $url;
	
	return $url;
}

// Devuelve el color segun el estado del usuario.
function ObtenerColorEstadoUsuario($Active){
	$strResult = "";
	
	switch($Active){
		case "Y":
			$strResult = "show";
			break;
		case "N":
			$strResult = "hide";
			break;
		case "P":
			$strResult = "pendAprobacion";
			break;
		default:
			$strResult = "pendAceptacion";
			break;
	}
	
	return $strResult;
}


function GetRows($handle) 
{   
    /* 
       This function emulates the ASP GetRows function. It creates a 2 dimensional 
       array of the data set where the : 

       1st dimension is the row number of the data 
       2nd dimension are the data fields 

       Returns a two dimensional array if there are record or false if no records 
       come out of the query 
    */ 

    if (mysql_num_rows($handle)>0){ 

        //initialize the array 
        $RsArray1   = array(); 

        //loop thru the recordset 
        while ($rows = mysql_fetch_array($handle)) 
        { 
            $RsArray1[] = $rows; 
        } //wend 
        return $RsArray1; 
    }else{ 
        //no records in recordset so return false 
        return null; 
    } //end if 
    //close the connection 
    mysql_close($handle); 

} //end function 

//Corta la cadena y le agrega "..."
function corta_texto($texto, $num) { 
 $b = false;
 //$txt = (strlen($texto) > $num) ? substr($texto,0,$num)."..." : $texto;
 $txt = $texto;
 if(strlen($texto) > $num){
	 $Aux = $num;
	 while($b == false && $Aux < strlen($texto)){
		if($texto{$Aux} == ' '){
			$b = true;
		}
		$Aux++;
	 }
	 $txt = substr($texto,0,$Aux - 1)."...";
 }
 return $txt;
}   

// Devuelve el estado del usuario.
function ObtenerNomEstadoTramite($Active){
	$strResult = "";
	
	switch($Active){
		case "F":
			$strResult = "Finalizado";
			break;
		case "N":
			$strResult = "Nuevo";
			break;
		case "E":
			$strResult = "En trámite";
			break;
	}
	
	return $strResult;
}   

// Devuelve el estado del usuario.
function ObtenerColorEstadoTramite($Active){
	$strResult = "";
	
	switch($Active){
		case "F":
			$strResult = "background-color:#1c540d;color:white;";
			break;
		case "N":
			$strResult = "background-color:#97499e;color:white;";
			break;
		case "E":
			$strResult = "background-color:#dec628;";
			break;
	}
	
	return $strResult;
}      

function compararFechas($primera, $segunda)   
{   
  $valoresPrimera = explode ("/", $primera);      
  $valoresSegunda = explode ("/", $segunda);    
  $diaPrimera    = $valoresPrimera[0];     
  $mesPrimera  = $valoresPrimera[1];     
  $anyoPrimera   = $valoresPrimera[2];    
  $diaSegunda   = $valoresSegunda[0];     
  $mesSegunda = $valoresSegunda[1];     
  $anyoSegunda  = $valoresSegunda[2];   
  $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);     
  $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);        
  if(!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)){   
    // "La fecha ".$primera." no es válida";   
    return 0;   
  }elseif(!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)){   
    // "La fecha ".$segunda." no es válida";   
    return 0;   
  }else{   
    return  $diasPrimeraJuliano - $diasSegundaJuliano;   
  }    
}   

// Funcion que encripta un valor.
function encrypt($string)
{
   	if(!$string || $string == ""){return "";}
	
	$key = '123456789';
	$result = '';

	for($i=0; $i<strlen($string); $i++)
	{
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}
	
	return urlencode(base64_encode($result));
}

// Funcion que decripta un valor.
function decrypt($string)
{
	if(!$string || $string == ""){return "";}
	
	$key = '123456789';
	$result = '';
	$string = base64_decode(urldecode($string));
	for($i=0; $i<strlen($string); $i++)
	{
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}
	
	return $result;
}


function InArray($Val, $Lista)
{
	/*
	$bResult = false;
	
	foreach(split(",", $Lista) as $str)
	{
		if(strtolower($str) == strtolower($Val))
		{
			$bResult = true;
			break;
		}
	}
	
	return $bResult;
	*/
	return in_array($Val, explode(",", $Lista));
}

function ObtenerInputTxt($Nombre, $Valor, $MaxLength = 0, $TabIndex = 0, $onBlur = "", $onKeyUp = "")
{
?>
	<input type="text" id="<?=$Nombre?>" name="<?=$Nombre?>" class="publicite" value="<?=$Valor?>" maxlength="<?=$MaxLength?>" <? if($TabIndex > 0){ ?> tabindex="<?=$TabIndex?>"<? } ?><? if($onBlur <> ""){ ?> onblur="<?=$onBlur?>"<? } ?><? if($onKeyUp != ""){ ?> onKeyUp="<?=$onKeyUp?>"<? } ?> />
<?
}

function ObtenerInputFile($Nombre, $TabIndex = 0)
{
?>
	<input type="file" id="<?=$Nombre?>" name="<?=$Nombre?>" class="publicite" <? if($TabIndex > 0){ ?>tabindex="<?=$TabIndex?>"<? } ?> />
<?
}

function ObtenerInputTextarea($Nombre, $Valor, $MaxLength = 0, $TabIndex = 0, $onKeyUp = "")
{
?>
	<textarea id="<?=$Nombre?>" name="<?=$Nombre?>" class="videos" <? if($TabIndex > 0){ ?>tabindex="<?=$TabIndex?>"<? } ?> <? if($MaxLength > 0){ ?> onKeyPress="LimitTextBoxValue(this, <?=$MaxLength?>, 'Solo puede ingresar <?=$MaxLength?> caracteres.');"<? } ?><? if($onKeyUp != ""){ ?> onKeyUp="<?=$onKeyUp?>"<? } ?>><?=$Valor?></textarea>
<?
}

// Retorna options con los meses de actividad.
function ObtenerOptionsMesActividad($sel = 0)
{
	// Obtengo la fecha actual.
	$strFechaActual = date("d/m/Y", time());
	// Obtengo el mes actual.
	$iMesActual = date("m", time());
	?>
	<option value="<?=$iMesActual?>"><?=ObtenerNombreMes($iMesActual)?></option>
<?	// Recorro la cantidad de meses que dura el aviso.
	// En este caso, son hasta 2 meses.
	for($i=1; $i<=2; $i++)
	{
		$iMesSiguiente = sumaMes($strFechaActual, $i, 'm'); ?>
		<option value="<?=$iMesSiguiente?>"><?=ObtenerNombreMes($iMesSiguiente)?></option>
<?	}
}

// Retorna options con los años de actividad.
function ObtenerOptionsAnioActividad($sel = 0)
{
	// Obtengo el mes actual.
	$iMesActual = date("m", time());
	// Obtengo el año actual.
	$iAnio 		= date("Y", time());
	?>
	<option value="<?=$iAnio?>" <? if($iAnio == $sel){ ?>selected="selected"<? } ?>><?=$iAnio?></option>
<?	// Verifico si el mes actual es Noviembre o Diciembre
	//	agrego para que pueda seleccionar el año siguiente
	if($iMesActual == 11 || $iMesActual == 12)
	{
		$iAnio++;
		?>
		<option value="<?=$iAnio?>" <? if($iAnio == $sel){ ?>selected="selected"<? } ?>><?=$iAnio?></option>
		<?
	}
}

//Obtiene el combo de las universidades de un usuario logueado.
//Si el usuario no está logueado devuelve todas las universidades.
//PARAMETROS:
//$strDefecto: Texto del valor por defecto del combo
//$strClientFunction = Funciones Cliente. Ej: OnChange='Validar();'
//$iIdUsuario: Usuario logueado
//$iIduniversidad: Universidad seleccionada
function ObtenerComboUniversidad($iIdUsuario = "", $iIdUniversidad = "", $strClientFunction = "", $strDefecto = "--Todas--", $strCabecera = "Y", $strClass="AcomodaDdlFiltro"){
	if($strCabecera == "Y"){?><select class="<?=$strClass?>" name="cmbUniversidad" id="cmbUniversidad" <?=$strClientFunction?>><? }
	?><option value=""><?=$strDefecto?></option><?
	
	$strSQLCommUniversidades = " SELECT DISTINCT U.universidad_id, U.universidad_titulo " .
							   " FROM universidad AS U ";
	if($iIdUsuario != "" && is_numeric($iIdUsuario)){
		$strSQLCommUniversidades = $strSQLCommUniversidades . 
								   " INNER JOIN facultad AS F ON (F.universidad_id = U.universidad_id AND F.activo = 'Y') " . 
								   " INNER JOIN carrera AS R ON (R.facultad_id = F.facultad_id AND R.activo = 'Y') " . 
								   " INNER JOIN materia AS MA ON (MA.carrera_id = R.carrera_id AND MA.activo = 'Y') " . 
								   " INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
								   " INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($iIdUsuario) . ") ";
				 
	}
	$strSQLCommUniversidades = $strSQLCommUniversidades . " WHERE U.activo = 'Y' " .
							   " ORDER BY U.universidad_titulo ";
	
	$ResultUniversidades = mysql_query($strSQLCommUniversidades);
	if(mysql_num_rows($ResultUniversidades)){
		while($oRs = mysql_fetch_array($ResultUniversidades))
		{
			?><option value="<?=$oRs["universidad_id"]?>"<? if(trim("".$iIdUniversidad) == trim("".$oRs["universidad_id"])){?> selected="selected"<? }?>><?=$oRs["universidad_titulo"]?></option><?
		}
	}
	mysql_free_result($ResultUniversidades);
	
	if($strCabecera == "Y"){?></select><? }
}


//Obtiene el combo de las facultades de un usuario logueado.
//Si el usuario no está logueado devuelve todas las facultades.
//PARAMETROS:
//$strDefecto: Texto del valor por defecto del combo
//$strClientFunction = Funciones Cliente. Ej: OnChange='Validar();'
//$iIdUsuario: Usuario logueado
//$iIduniversidad: Universidad seleccionada
//$iIdFacultad: Facultad seleccionada
function ObtenerComboFacultad($iIdUsuario = "", $iIdFacultad = "", $iIdUniversidad = "", $strClientFunction = "", $strDefecto = "--Todas--", $strCabecera = "Y",  $strClass="AcomodaDdlFiltro"){
	if($strCabecera == "Y"){?><select class="<?=$strClass?>" name="cmbFacultad" id="cmbFacultad" <?=$strClientFunction?> ><? }
	?><option value=""><?=$strDefecto?></option><?
	
	$strSQLCommFacultades = " SELECT DISTINCT F.facultad_id, F.facultad_titulo " .
							   " FROM facultad AS F ";
	if($iIdUsuario != "" && is_numeric($iIdUsuario)){
		$strSQLCommFacultades = $strSQLCommFacultades . 
								   " INNER JOIN carrera AS R ON (R.facultad_id = F.facultad_id AND R.activo = 'Y') " . 
								   " INNER JOIN materia AS MA ON (MA.carrera_id = R.carrera_id AND MA.activo = 'Y') " . 
								   " INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
								   " INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($iIdUsuario) . ") ";
				 
	}
	$strSQLCommFacultades = $strSQLCommFacultades . " WHERE F.activo = 'Y' ";
	if($iIdUniversidad != "" && is_numeric($iIdUniversidad)){
		$strSQLCommFacultades = $strSQLCommFacultades . " AND F.universidad_id = " . SafeSql($iIdUniversidad);
	}
	$strSQLCommFacultades = $strSQLCommFacultades . " ORDER BY F.facultad_titulo ";
	
	$ResultFacultades = mysql_query($strSQLCommFacultades);
	if(mysql_num_rows($ResultFacultades)){
		while($oRs = mysql_fetch_array($ResultFacultades))
		{
			?><option value="<?=$oRs["facultad_id"]?>"<? if(trim("".$iIdFacultad) == trim("".$oRs["facultad_id"])){?> selected="selected"<? }?>><?=$oRs["facultad_titulo"]?></option><?
		}
	}
	mysql_free_result($ResultFacultades);
	
	if($strCabecera == "Y"){?></select><? }
}

//Obtiene el combo de las carreras de un usuario logueado.
//Si el usuario no está logueado devuelve todas las carreras.
//PARAMETROS:
//$strDefecto: Texto del valor por defecto del combo
//$strClientFunction = Funciones Cliente. Ej: OnChange='Validar();'
//$iIdUsuario: Usuario logueado
//$iIdFacultad: Facultad seleccionada
//$iIdCarrera: Carrera seleccionada
function ObtenerComboCarrera($iIdUsuario = "", $iIdCarrera = "", $iIdFacultad = "", $strClientFunction = "", $strDefecto = "--Todas--", $strCabecera = "Y",  $strClass="AcomodaDdlFiltro"){
	if($strCabecera == "Y"){?><select class="<?=$strClass?>" name="cmbCarrera" id="cmbCarrera" <?=$strClientFunction?>><? }
	?><option value=""><?=$strDefecto?></option><?
	
	$strSQLCommCarreras = " SELECT DISTINCT R.carrera_id, R.carrera_titulo " .
							   " FROM carrera AS R ";
	if($iIdUsuario != "" && is_numeric($iIdUsuario)){
		$strSQLCommCarreras = $strSQLCommCarreras . 
								   " INNER JOIN materia AS MA ON (MA.carrera_id = R.carrera_id AND MA.activo = 'Y') " . 
								   " INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
								   " INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($iIdUsuario) . ") ";
				 
	}
	$strSQLCommCarreras = $strSQLCommCarreras . " WHERE R.activo = 'Y' ";
	if($iIdFacultad != "" && is_numeric($iIdFacultad)){
		$strSQLCommCarreras = $strSQLCommCarreras . " AND R.facultad_id = " . SafeSql($iIdFacultad);
	}
	$strSQLCommCarreras = $strSQLCommCarreras . " ORDER BY R.carrera_titulo ";
	
	$ResultCarreras = mysql_query($strSQLCommCarreras);
	if(mysql_num_rows($ResultCarreras)){
		while($oRs = mysql_fetch_array($ResultCarreras))
		{
			?><option value="<?=$oRs["carrera_id"]?>"<? if(trim("".$iIdCarrera) == trim("".$oRs["carrera_id"])){?> selected="selected"<? }?>><?=$oRs["carrera_titulo"]?></option><?
		}
	}
	mysql_free_result($ResultCarreras);
	
	if($strCabecera == "Y"){?></select><? }
}

//Obtiene el combo de las materias de un usuario logueado.
//Si el usuario no está logueado devuelve todas las materias.
//PARAMETROS:
//$strDefecto: Texto del valor por defecto del combo
//$strClientFunction = Funciones Cliente. Ej: OnChange='Validar();'
//$iIdUsuario: Usuario logueado
//$iIdCarrera: Carrera seleccionada
//$iIdMateria: Materia seleccionada
function ObtenerComboMateria($iIdUsuario = "", $iIdMateria = "", $iIdCarrera = "", $strClientFunction = "", $strDefecto = "--Todas--", $strCabecera = "Y",  $strClass="AcomodaDdlFiltro"){
	if($strCabecera == "Y"){?><select class="<?=$strClass?>" name="cmbMateria" id="cmbMateria" <?=$strClientFunction?>><? }
	?><option value=""><?=$strDefecto?></option><?
	
	$strSQLCommMaterias = " SELECT DISTINCT MA.materia_id, MA.materia_titulo " .
							   " FROM materia AS MA ";
	if($iIdUsuario != "" && is_numeric($iIdUsuario)){
		$strSQLCommMaterias = $strSQLCommMaterias . 
								   " INNER JOIN comision AS C ON (C.materia_id = MA.materia_id AND C.activo = 'Y') " . 
								   " INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($iIdUsuario) . ") ";
				 
	}
	$strSQLCommMaterias = $strSQLCommMaterias . " WHERE MA.activo = 'Y' ";
	if($iIdCarrera != "" && is_numeric($iIdCarrera)){
		$strSQLCommMaterias = $strSQLCommMaterias . " AND MA.carrera_id = " . SafeSql($iIdCarrera);
	}
	$strSQLCommMaterias = $strSQLCommMaterias . " ORDER BY MA.materia_titulo ";
	$ResultMaterias = mysql_query($strSQLCommMaterias);
	if(mysql_num_rows($ResultMaterias)){
		while($oRs = mysql_fetch_array($ResultMaterias))
		{
			?><option value="<?=$oRs["materia_id"]?>"<? if(trim("".$iIdMateria) == trim("".$oRs["materia_id"])){?> selected="selected"<? }?>><?=$oRs["materia_titulo"]?></option><?
		}
	}
	mysql_free_result($ResultMaterias);
	
	if($strCabecera == "Y"){?></select><? }
}


//Obtiene el combo de las comisiones de un usuario logueado.
//Si el usuario no está logueado devuelve todas las comisiones.
//PARAMETROS:
//$strDefecto: Texto del valor por defecto del combo
//$strClientFunction = Funciones Cliente. Ej: OnChange='Validar();'
//$iIdUsuario: Usuario logueado
//$iIdMateria: Materia seleccionada
//$iIdComision: Comision seleccionada
function ObtenerComboComision($iIdUsuario = "", $iIdComision = "", $iIdMateria = "", $strClientFunction = "", $strDefecto = "--Todas--", $strCabecera = "Y", $strClass="AcomodaDdlFiltro" ){
	if($strCabecera == "Y"){?><select class="<?=$strClass?>" name="cmbComision" id="cmbComision" <?=$strClientFunction?>><? }
	?><option value=""><?=$strDefecto?></option><?
	
	$strSQLCommComisiones = " SELECT DISTINCT C.comision_id, C.comision_titulo, C.comision_codigo " .
							   " FROM comision AS C ";
	if($iIdUsuario != "" && is_numeric($iIdUsuario)){
		$strSQLCommComisiones = $strSQLCommComisiones . 
								   " INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($iIdUsuario) . ") ";
				 
	}
	$strSQLCommComisiones = $strSQLCommComisiones . " WHERE C.activo = 'Y' ";
	if($iIdMateria != "" && is_numeric($iIdMateria)){
		$strSQLCommComisiones = $strSQLCommComisiones . " AND C.materia_id = " . SafeSql($iIdMateria);
	}
	$strSQLCommComisiones = $strSQLCommComisiones . " ORDER BY C.comision_titulo ";
	
	$ResultComisiones = mysql_query($strSQLCommComisiones);
	if(mysql_num_rows($ResultComisiones)){
		while($oRs = mysql_fetch_array($ResultComisiones))
		{
			?><option value="<?=$oRs["comision_id"]?>"<? if(trim("".$iIdComision) == trim("".$oRs["comision_id"])){?> selected="selected"<? }?>><?=$oRs["comision_titulo"]?><? if($oRs["comision_codigo"] != ""){?>(<?=$oRs["comision_codigo"]?>)<? }?></option><?
		}
	}
	mysql_free_result($ResultComisiones);
	
	if($strCabecera == "Y"){?></select><? }
}

//Obtiene el combo de los grupos de un usuario logueado.
//Si el usuario no está logueado devuelve todos las grupos.
//PARAMETROS:
//$strDefecto: Texto del valor por defecto del combo
//$strClientFunction = Funciones Cliente. Ej: OnChange='Validar();'
//$iIdUsuario: Usuario logueado
//$iIdComision: Comision seleccionada
//$iIdGrupo: Grupo seleccionado
function ObtenerComboGrupo($iIdUsuario = "", $iIdGrupo = "", $iIdComision = "", $strClientFunction = "", $strDefecto = "--Todos--", $strCabecera = "Y", $strClass="AcomodaDdlFiltro"){
	if($strCabecera == "Y"){?><select class="<?=$strClass?>" name="cmbGrupo" id="cmbGrupo" <?=$strClientFunction?>><? }
	?><option value=""><?=$strDefecto?></option><?
	
	$strSQLCommGrupos = " SELECT DISTINCT G.grupo_id, G.grupo_nombre " .
							   " FROM grupo AS G ";
	if($iIdUsuario != "" && is_numeric($iIdUsuario)){
		$strSQLCommGrupos = $strSQLCommGrupos . 
								   " INNER JOIN usuario_grupo AS UG ON (G.grupo_id = UG.grupo_id AND UG.usuario_id = " . SafeSql($iIdUsuario) . ") ";
				 
	}
	$strSQLCommGrupos = $strSQLCommGrupos . " WHERE G.activo = 'Y' ";
	if($iIdComision != "" && is_numeric($iIdComision)){
		$strSQLCommGrupos = $strSQLCommGrupos . " AND G.comision_id = " . SafeSql($iIdComision);
	}
	$strSQLCommGrupos = $strSQLCommGrupos . " ORDER BY G.grupo_nombre ";
	
	$ResultGrupos = mysql_query($strSQLCommGrupos);
	if(mysql_num_rows($ResultGrupos)){
		while($oRs = mysql_fetch_array($ResultGrupos))
		{
			?><option value="<?=$oRs["grupo_id"]?>"<? if(trim("".$iIdGrupo) == trim("".$oRs["grupo_id"])){?> selected="selected"<? }?>><?=$oRs["grupo_nombre"]?></option><?
		}
	}
	mysql_free_result($ResultGrupos);
	
	if($strCabecera == "Y"){?></select><? }
}

//Obtiene el combo de los usuarios del grupo de un usuario logueado.
//Si el usuario no está logueado devuelve los usuarios de todos las grupos.
//PARAMETROS:
//$strDefecto: Texto del valor por defecto del combo
//$strClientFunction = Funciones Cliente. Ej: OnChange='Validar();'
//$iIdUsuario: Usuario logueado
//$iIdGrupo: Grupo seleccionado
//$iIdUser: Usuario seleccionado
function ObtenerComboUsuario($iIdUsuario = "", $iIdUser = "", $iIdGrupo = "", $strClientFunction = "", $strDefecto = "--Todos--", $strCabecera = "Y", $strClass="AcomodaDdlFiltro"){
	if($strCabecera == "Y"){?><select class="<?=$strClass?>" name="cmbUsuario[]" id="cmbUsuario" <?=$strClientFunction?> multiple="multiple" style="height:150px"><? }
	?><option value=""><?=$strDefecto?></option><?
	
	$strSQLCommUsuarios = " SELECT DISTINCT U.usuario_id, U.usuario_nombre, U.usuario_apellido " .
						" FROM usuario AS U " . 
						" INNER JOIN usuario_grupo AS UG ON UG.usuario_id = U.usuario_id ";
	if($iIdUsuario != "" && is_numeric($iIdUsuario)){
		$strSQLCommUsuarios = $strSQLCommUsuarios . 
								   " INNER JOIN grupo AS G ON (G.grupo_id = UG.grupo_id AND G.activo = 'Y')" .
								   " INNER JOIN usuario_comision AS UC ON (G.comision_id = UC.comision_id AND UC.activo = 'Y' AND UC.usuario_id = " . SafeSql($iIdUsuario) . ") ";
				 
	}
	$strSQLCommUsuarios = $strSQLCommUsuarios . " WHERE U.activo = 'Y' ";
	
	if($iIdGrupo == ""){
		$iIdGrupo = "0";
	}
						
	if($iIdGrupo != "" && is_numeric($iIdGrupo)){
		$strSQLCommUsuarios = $strSQLCommUsuarios . " AND UG.grupo_id = " . SafeSql($iIdGrupo);
	}
	$strSQLCommUsuarios = $strSQLCommUsuarios . " ORDER BY U.usuario_nombre, U.usuario_apellido ";
	
	$ResultUsuarios = mysql_query($strSQLCommUsuarios);
	if(mysql_num_rows($ResultUsuarios)){
		while($oRs = mysql_fetch_array($ResultUsuarios))
		{
			?><option value="<?=$oRs["usuario_id"]?>"<? if(trim("".$iIdUser) == trim("".$oRs["usuario_id"])){?> selected="selected"<? }?>><?=$oRs["usuario_nombre"]?> <?=$oRs["usuario_apellido"]?></option><?
		}
	}
	mysql_free_result($ResultUsuarios);
	
	if($strCabecera == "Y"){?></select><? }
}

//Obtiene el combo de los usuarios del grupo de un usuario logueado.
//Si el usuario no está logueado devuelve los usuarios de todos las grupos.
//PARAMETROS:
//$strDefecto: Texto del valor por defecto del combo
//$strClientFunction = Funciones Cliente. Ej: OnChange='Validar();'
//$iIdUsuario: Usuario logueado
//$iIdGrupo: Grupo seleccionado
//$iIdUser: Usuario seleccionado
function ObtenerComboUsuarioPorComision($iIdUsuario = "", $iIdUser = "", $iIdComision = "", $strClientFunction = "", $strDefecto = "--Todos--", $strCabecera = "Y", $strClass="AcomodaDdlFiltro"){
	if($strCabecera == "Y"){?><select class="<?=$strClass?>" name="cmbUsuario[]" id="cmbUsuario" <?=$strClientFunction?> multiple="multiple" style="height:150px"><? }
	?><option value=""><?=$strDefecto?></option><?
	
	$strSQLCommUsuarios = " SELECT DISTINCT U.usuario_id, U.usuario_nombre, U.usuario_apellido " .
						" FROM usuario AS U " . 
						" INNER JOIN usuario_comision AS UC ON (UC.usuario_id = U.usuario_id AND UC.activo = 'Y') " . 
						" INNER JOIN usuario_comision AS UC2 ON (UC.comision_id = UC2.comision_id AND UC2.activo = 'Y') " . 
						" WHERE U.activo = 'Y' ";
	if($iIdUsuario != "" && is_numeric($iIdUsuario)){
		$strSQLCommUsuarios = $strSQLCommUsuarios . 
								   " AND UC2.usuario_id = " . SafeSql($iIdUsuario);
				 
	}
	
	if($iIdComision == ""){
		$iIdComision = "0";
	}
						
	if($iIdComision != "" && is_numeric($iIdComision)){
		$strSQLCommUsuarios = $strSQLCommUsuarios . " AND UC.comision_id = " . SafeSql($iIdComision);
	}
	$strSQLCommUsuarios = $strSQLCommUsuarios . " ORDER BY U.usuario_nombre, U.usuario_apellido ";
	$ResultUsuarios = mysql_query($strSQLCommUsuarios);
	if(mysql_num_rows($ResultUsuarios)){
		while($oRs = mysql_fetch_array($ResultUsuarios))
		{
			?><option value="<?=$oRs["usuario_id"]?>"<? if(trim("".$iIdUser) == trim("".$oRs["usuario_id"])){?> selected="selected"<? }?>><?=$oRs["usuario_nombre"]?> <?=$oRs["usuario_apellido"]?></option><?
		}
	}
	mysql_free_result($ResultUsuarios);
	
	if($strCabecera == "Y"){?></select><? }
}

//Obtiene los integrantes de un grupo.
//PARAMETROS:
//$iIdGrupo: ID del grupo
function ObtenerGrupoIntegrantes($iIdGrupo = "0"){
	$iInt = 0;
	$strIntegrantes = "";
	$strSQLCommIntegrantes = " SELECT DISTINCT U.usuario_nombre, U.usuario_apellido " .
							 " FROM usuario_grupo AS UG " .
							 " INNER JOIN usuario AS U ON (U.usuario_id = UG.usuario_id) " . 
							 " WHERE U.activo = 'Y' " . 
							 " AND UG.grupo_id = " . SafeSql($iIdGrupo) . 
							 " ORDER BY U.usuario_nombre, U.usuario_apellido ";
	//echo($strSQLCommIntegrantes);exit();
	$ResultIntegrantes = mysql_query($strSQLCommIntegrantes);
	if(mysql_num_rows($ResultIntegrantes)){
		while($oRs2 = mysql_fetch_array($ResultIntegrantes))
		{
			if($iInt != 0){
				$strIntegrantes = $strIntegrantes . ", ";
			}
			$strIntegrantes = $strIntegrantes . $oRs2["usuario_nombre"] . " " . $oRs2["usuario_apellido"];
			$iInt ++;
		}
	}
	mysql_free_result($ResultIntegrantes);
	
	return $strIntegrantes;
}

//Obtiene los ids de los integrantes de un grupo.
//PARAMETROS:
//$iIdGrupo: ID del grupo
function ObtenerGrupoIdIntegrantes($iIdGrupo = "0"){
	$iInt = 0;
	$strIntegrantes = "";
	$strSQLCommIntegrantes = " SELECT DISTINCT U.usuario_id " .
							 " FROM usuario_grupo AS UG " .
							 " INNER JOIN usuario AS U ON (U.usuario_id = UG.usuario_id) " . 
							 " WHERE U.activo = 'Y' " . 
							 " AND UG.grupo_id = " . SafeSql($iIdGrupo) . 
							 " ORDER BY U.usuario_nombre, U.usuario_apellido ";
	//echo($strSQLCommIntegrantes);exit();
	$ResultIntegrantes = mysql_query($strSQLCommIntegrantes);
	if(mysql_num_rows($ResultIntegrantes)){
		while($oRs2 = mysql_fetch_array($ResultIntegrantes))
		{
			if($iInt != 0){
				$strIntegrantes = $strIntegrantes . ",";
			}
			$strIntegrantes = $strIntegrantes . $oRs2["usuario_id"];
			$iInt ++;
		}
	}
	mysql_free_result($ResultIntegrantes);
	
	return $strIntegrantes;
}


//retorna un arreglo de archivos de un directorio dado
//que cumplan con el nombre indicado en $filtro
function lee_archivos($directorio,$filtro)
{
  $archs=array();
  $midir=opendir($directorio);
  $i=0;
  $filtro = substr($filtro, 0, strlen($filtro) - strlen(strrchr($filtro,".")));//Obtengo el nombre del archivo del filtro
  while($archivo=readdir($midir))
  {
	 $ext=strrchr($archivo,".");//Obtengo la extension del archivo
	 $nom=substr($archivo, 0, strlen($archivo) - strlen(strrchr($archivo,".")));//Obtengo el nombre sin la extension del archivo
	 //echo($ext . " - " .  $archivo . " - " .  $nom . " - " . substr_count($nom, $filtro) . "<br>");
	 if (!is_dir($archivo) && (substr_count($nom, $filtro) > 0 || !$filtro))
		$archs[$i++]=$archivo;
  }
  return $archs;
}

//Obtener mails de envio
//$strTabla:
//U=Universidad
//F=Facultad
//R=Carrera
//M=Materia
//C=Comision
//G=Grupo
//P=Usuario
function ObtenerMails($iId, $strTabla, $strPerfil = "'P','Y','L'"){
	$iInt = 0;
	$strMails = "";
	
	switch($strTabla){
		case "U":
			$strSQLObtenerMails = " SELECT U.usuario_email " . 
								" FROM usuario AS U " . 
								" INNER JOIN usuario_comision AS UC ON (UC.usuario_id = U.usuario_id AND UC.activo = 'Y') " .
								" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
								" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
								" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
								" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
								" INNER JOIN universidad AS UN ON (UN.universidad_id = F.universidad_id AND UN.activo = 'Y') " .
								" WHERE U.activo = 'Y' " .
								" AND U.usuario_notificaciones = 'Y' " .
								" AND UC.usuario_tipo IN (" . $strPerfil . ")" . 
								" AND UN.universidad_id = " . SafeSql($iId); 
				
			break;
		case "F":
			$strSQLObtenerMails = " SELECT U.usuario_email " . 
								" FROM usuario AS U " . 
								" INNER JOIN usuario_comision AS UC ON (UC.usuario_id = U.usuario_id AND UC.activo = 'Y') " .
								" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
								" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
								" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
								" INNER JOIN facultad AS F ON (F.facultad_id = R.facultad_id AND F.activo = 'Y') " . 
								" WHERE U.activo = 'Y' " .
								" AND U.usuario_notificaciones = 'Y' " .
								" AND UC.usuario_tipo IN (" . $strPerfil . ")" . 
								" AND F.facultad_id = " . SafeSql($iId); 
			break;
		case "R":
			$strSQLObtenerMails = " SELECT U.usuario_email " . 
								" FROM usuario AS U " . 
								" INNER JOIN usuario_comision AS UC ON (UC.usuario_id = U.usuario_id AND UC.activo = 'Y') " .
								" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
								" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
								" INNER JOIN carrera AS R ON (R.carrera_id = MA.carrera_id AND R.activo = 'Y') " . 
								" WHERE U.activo = 'Y' " .
								" AND U.usuario_notificaciones = 'Y' " .
								" AND UC.usuario_tipo IN (" . $strPerfil . ")" . 
								" AND R.carrera_id = " . SafeSql($iId); 
			break;
		case "M":
			$strSQLObtenerMails = " SELECT U.usuario_email " . 
								" FROM usuario AS U " . 
								" INNER JOIN usuario_comision AS UC ON (UC.usuario_id = U.usuario_id AND UC.activo = 'Y') " .
								" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
								" INNER JOIN materia AS MA ON (MA.materia_id = C.materia_id AND MA.activo = 'Y') " . 
								" WHERE U.activo = 'Y' " .
								" AND U.usuario_notificaciones = 'Y' " .
								" AND UC.usuario_tipo IN (" . $strPerfil . ")" . 
								" AND MA.materia_id = " . SafeSql($iId); 
			break;
		case "C":
			$strSQLObtenerMails = " SELECT U.usuario_email " . 
								" FROM usuario AS U " . 
								" INNER JOIN usuario_comision AS UC ON (UC.usuario_id = U.usuario_id AND UC.activo = 'Y') " .
								" INNER JOIN comision AS C ON (C.comision_id = UC.comision_id AND C.activo = 'Y') " . 
								" WHERE U.activo = 'Y' " .
								" AND U.usuario_notificaciones = 'Y' " .
								" AND UC.usuario_tipo IN (" . $strPerfil . ")" . 
								" AND C.comision_id = " . SafeSql($iId); 
			break;
		case "G":
			$strSQLObtenerMails = " SELECT U.usuario_email " . 
								" FROM usuario AS U " . 
								" INNER JOIN usuario_grupo AS UG ON (UG.usuario_id = U.usuario_id) " .
								" INNER JOIN grupo AS G ON (G.grupo_id = UG.grupo_id) " .
								" INNER JOIN comision AS C ON (G.comision_id = C.comision_id AND C.activo = 'Y') " .
								" INNER JOIN usuario_comision AS UC ON (UC.comision_id = C.comision_id AND UC.usuario_id = U.usuario_id AND UC.activo = 'Y') " .
								" WHERE U.activo = 'Y' " .
								" AND U.usuario_notificaciones = 'Y' " .
								" AND UG.grupo_id = " . SafeSql($iId); 
			break;
		case "P":
			$strSQLObtenerMails = " SELECT U.usuario_email " . 
								" FROM usuario AS U " . 
								" WHERE U.activo = 'Y' " .
								" AND U.usuario_notificaciones = 'Y' " .
								" AND U.usuario_id = " . SafeSql($iId); 
			break;
		default:
			$strSQLObtenerMails = "";
			break;
	}	
	//echo($strSQLObtenerMails);exit();
	if($strSQLObtenerMails != ""){
		$ResultObtenerMails = mysql_query($strSQLObtenerMails);
		if(mysql_num_rows($ResultObtenerMails)){
			while($oRs2 = mysql_fetch_array($ResultObtenerMails))
			{
				if($iInt != 0){
					$strMails = $strMails . ",";
				}
				$strMails = $strMails . $oRs2["usuario_email"];
				$iInt ++;
			}
		}
		mysql_free_result($ResultObtenerMails);
	}
	
	return $strMails;
}
?>