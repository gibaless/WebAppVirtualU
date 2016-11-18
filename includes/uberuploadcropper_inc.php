<? // Parámetros de configuración.
if(!isset($thumbTxtImageBrowse))
	$thumbTxtImageBrowse = "";

if(!isset($thumbPath))
	$thumbPath = "";

if(!isset($thumbFileDesc))
	$thumbFileDesc = 'Imagenes JPG';

if(!isset($thumbFileExt))
	$thumbFileExt = '*.jpg;*.jpeg';

if(!isset($thumbX1))
	$thumbX1 = 0;

if(!isset($thumbY1))
	$thumbY1 = 0;

if(!isset($thumbCropWidth))
	$thumbCropWidth = 0;

if(!isset($thumbCropHeight))
	$thumbCropHeight = 0;

if(!isset($thumbHiddenToPost))
	$thumbHiddenToPost = "";

if(!isset($thumbContent))
	$thumbContent = "";

// TRUE: Concatena el contenido.
// FALSE: Elimina el contenido y agrega el nuevo item.
if(!isset($thumbAcceptMultiImages))
	$thumbAcceptMultiImages = false;
	
if(!isset($thumbMulti))
	$thumbMulti = true;
	
if(!isset($thumbAuto))
	$thumbAuto = true;
	
if(!isset($thumbWidth))
	$thumbWidth = 0;
	
if(!isset($thumbHeight))
	$thumbHeight = 0;
	
// TRUE: Abre la imagen con el lightwindows.
if(!isset($thumbPreview))
	$thumbPreview = false;

// TRUE: Genera un nombre random.
if(!isset($thumbGenerarNombreUnico))
	$thumbGenerarNombreUnico = true;
	
// Texto el cual se le le concatena al nombre de la imagen para que sea unica.
$thumbNombreUnico = "";
if(isset($thumbGenerarNombreUnico) && $thumbGenerarNombreUnico)
	$thumbNombreUnico = rand(1, 10000); // date('YmdHis',time());
?>
<script type="text/javascript">
	$(function() {
		$('#<?=$thumbTxtImageBrowse?>').uberuploadcropper({
			//---------------------------------------------------
			// uploadify options..
			//---------------------------------------------------
			'uploader'  : 'upload_crop/scripts/uploadify.swf',
			'script'    : 'upload_crop/uploadify.php',
			'cancelImg' : 'upload_crop/cancel.png',
			'multi'     : <?=($thumbMulti ? 'true' : 'false')?>,
			'auto'      : <?=($thumbAuto ? 'true' : 'false')?>,
			'folder'    : 'dummy|<?=$thumbNombreUnico?>|<?=$thumbPath?>',
			'fileDesc'  : '<?=$thumbFileDesc?>',
			'fileExt'   : '<?=$thumbFileExt?>',
			//---------------------------------------------------
			//now the cropper options..
			//---------------------------------------------------
			'widthOriginal': <?=$thumbCropWidth?>,
			'heightOriginal': <?=$thumbCropHeight?>,
			'aspectRatio': <?=$thumbCropWidth?>/<?=$thumbCropHeight?>, 
			'minSize': [ 0, 0 ], 
			'allowSelect': false,			//can reselect
			'allowResize' : true,			//can resize selection
			'setSelect': [ <?=$thumbX1?>, <?=$thumbY1?>, <?=$thumbCropWidth?>, <?=$thumbCropHeight?> ],	//these are the dimensions of the crop box x1,y1,x2,y2
			'cropScript': 'upload_crop/crop.php',
			'onError': function (a, b, c, d) {
						if (d.status == 404)
							alert('Could not find upload script. Use a path relative to: '+'<?= getcwd() ?>');
						else if (d.type === "HTTP")
							alert('error '+d.type+": "+d.status+"\nInfo: " + d.info);
						else if (d.type ==="File Size")
							alert(c.name+' '+d.type+' Limit: '+Math.round(d.sizeLimit/1024)+'KB');
						else
							alert('error '+d.type+": "+d.text);
					},
			'onComplete': function(imgs,data){ 
				var randomnumber=Math.floor(Math.random()*1000);
				
				var onClickParams = "'ContenedorImagenGaleria" + randomnumber + "', '<?=$thumbPath?>" + imgs[0].name + "'";
				var oImgEliminar = '<img id="imgEliminarImagenGaleria' + randomnumber + '" src="imagenes/icono-eliminar.gif" class="imgEliminarImagenGaleria" onclick="imgEliminarImagenGaleria_onClick(' + onClickParams + ');" alt="Borrar imágen" title="Borrar imágen" />';
				
				var oImg = '';
				
			<?	if($thumbPreview)
				{
					?>
					oImg += '<a href="<?=$thumbPath?>'+imgs[0].name+'?d=' + (new Date()).getTime() + '" <?=(isset($thumbGroup) ? 'rel="Group' . $thumbGroup . '"' : '')?> class="lightbox">';
					<?
				}
				?>
				
				oImg += '<img id="ImagenGaleria' + randomnumber + '" src="<?=$thumbPath?>'+imgs[0].name+'?d=' + (new Date()).getTime() + '" <?=($thumbWidth > 0 ? 'width="' . $thumbWidth . '"' : '');?> <?=($thumbHeight > 0 ? 'height="' . $thumbHeight . '"' : '');?>height="70" class="imgGaleria" />';
				
			<?	if($thumbPreview)
				{
					?>
					oImg += '</a>';
					<?
				}
				?>
				
				var oImgHidden = '<input type="hidden" name="<?=$thumbHiddenToPost?>" value="' + imgs[0].name + '" />';
				
				var oThumb = '<div class="ContenedorFotosCarga">' +
							'	<div id="ContenedorImagenGaleria' + randomnumber + '" class="FotoCarga">' +
							'		<span class="boxcaption">' +
										oImgEliminar +
							'		</span> ' +
									oImg + oImgHidden +
							'	</div>' +
							'</div>';
				
				<?	if($thumbAcceptMultiImages)
					{
						?>$('#<?=$thumbContent?>').html($('#<?=$thumbContent?>').html() + oThumb);<?
					}else{
						?>$('#<?=$thumbContent?>').html(oThumb);<?
					} ?>
				
				ActualizarThumb();
			}
		});
		
	});
</script>