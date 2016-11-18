<?
session_start();

/* INCLUYO ARCHIVO DE CONFIGURACIÓN. */
include "../includes/sitesettings_inc.php";
include "../includes/class_resize.php";

/*############################################
$thumb=new thumbnail("./shiegege.jpg");			// generate image_file, set filename to resize/resample
$thumb->size_width(100);						// set width for thumbnail, or
$thumb->size_height(300);						// set height for thumbnail, or
$thumb->size_auto(200);							// set the biggest width or height for thumbnail
$thumb->jpeg_quality(75);						// [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
$thumb->show();									// show your thumbnail
$thumb->save("./huhu.jpg");						// save your thumbnail to file
*/############################################

// DIRECTORIO DONDE SE ALOJAN LOS ARCHIVOS.
$PathFile 	= realpath("..") . '/' . $_REQUEST["f"];

// PARAMETROS
$WIDTH 		= (isset($_REQUEST["w"]) && $_REQUEST["w"] != "" ? $_REQUEST["w"] : "");
$HEIGHT 	= (isset($_REQUEST["h"]) && $_REQUEST["h"] != "" ? $_REQUEST["h"] : "");
$AUTO_SIZE 	= (isset($_REQUEST["a"]) && $_REQUEST["a"] != "" ? $_REQUEST["a"] : "");
$QUALITY 	= (isset($_REQUEST["q"]) && $_REQUEST["q"] != "" ? $_REQUEST["q"] : 75);

$thumb=new thumbnail($PathFile);
// mode resize.
if($HEIGHT != "") {
    $thumb->size_height($HEIGHT);
}

if ($WIDTH != "") {
    $thumb->size_width($WIDTH);
}

if ($AUTO_SIZE != "") {
    $thumb->size_auto($AUTO_SIZE);
}
	
$thumb->jpeg_quality($QUALITY);
$thumb->show();
?>