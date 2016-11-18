<?
// Seteo Constantes
$APERTURA_SENTENCIA_ASP = chr("60") . "?";
$CIERRE_SENTENCIA_ASP = "?" . chr("62");

// Crea el directorio de la Categoria


function CrearDirectorioCategoria($NombreCategoria, $NombreAnteriorCategoria, $PathCategoriaPadre, $CodigoCategoria)
{
	
	$strPathCategoria = realpath("..") . $PathCategoriaPadre . "/" . $NombreCategoria;
	
	if($NombreAnteriorCategoria == "")
	{	// Nuevo Directorio
		if(is_dir($strPathCategoria))
		{
			mkdir("albums/".$checknr, 0777); 
    		chmod ("albums/".$checknr, 0777); 

			
			oFs.CreateFolder(strPathCategoria)
			CrearArchivoCategoria strPathCategoria, $CodigoCategoria, Ubound(Split($PathCategoriaPadre, "/"))
		}
	Else ' Se Renombra el directorio
		strNombreAnteriorCategoria = Server.MapPath("..") & $PathCategoriaPadre & "/" & $NombreAnteriorCategoria
		
		If (oFs.FolderExists(strNombreAnteriorCategoria)) Then
			oFs.MoveFolder strNombreAnteriorCategoria, strPathCategoria
		Else
			oFs.CreateFolder(strPathCategoria)
		End If
		CrearArchivoCategoria strPathCategoria, $CodigoCategoria, Ubound(Split($PathCategoriaPadre, "/"))
	End If
	Set oFs = Nothing
}

' Creo el archivo de categoria dentro del directorio correspondiente
Sub CrearArchivoCategoria(PathCategoria, $CodigoCategoria, Nivel)
	Set oFs = Server.CreateObject("Scripting.FileSystemObject")
	Dim strFileName
	strFileName = PathCategoria & "/index.asp"
	
	'If (Not oFs.FileExists(strFileName)) Then
		iIdCategoriaArch = $CodigoCategoria
		iIdCategoriaFiltro = ""
		strUpDirs = ""
		If (Nivel <= 0) Then 
			Nivel = 1 ' Es el caso de que sea un directorio Raiz
		Else
			Nivel = Nivel + 1
		End If
		For i=1 To Nivel
			strUpDirs = "../" & strUpDirs
		Next
		
		Set ObjConnCateg = Server.CreateObject("ADODB.Connection")
		ObjConnCateg.Mode = 3
		ObjConnCateg.Open Application("DBConnection")
		Set ObjRsCateg = Server.CreateObject("ADODB.RecordSet")
		strSQLComm = "SELECT * FROM categ_productos WHERE id_categ = " & $CodigoCategoria
		ObjRsCateg.Open strSQLComm, ObjConnCateg
		If ( Not (ObjRsCateg.BOF AND ObjRsCateg.EOF) ) Then
			If ( ObjRsCateg("es_filtro_categ") <> "" AND ObjRsCateg("es_filtro_categ") = "Y" ) Then
				iIdCategoriaArch = ObjRsCateg("id_categ_padre")
				iIdCategoriaFiltro = $CodigoCategoria
			End If
		End If
		ObjRsCateg.Close
		ObjConnCateg.Close
		Set ObjRsCateg = Nothing
		Set ObjConnCateg = Nothing
		
		Set oTextFile = oFs.CreateTextFile(strFileName, true)
		oTextFile.WriteLine APERTURA_SENTENCIA_ASP & "@ Language=VBScript " & CIERRE_SENTENCIA_ASP
		oTextFile.WriteLine APERTURA_SENTENCIA_ASP & " id_categoria=" & iIdCategoriaArch & " " & CIERRE_SENTENCIA_ASP
		If (iIdCategoriaFiltro <> "") Then
			oTextFile.WriteLine APERTURA_SENTENCIA_ASP & " id_categoria_filtro=" & iIdCategoriaFiltro & " " & CIERRE_SENTENCIA_ASP
		End If
		oTextFile.WriteLine "<!-- #include file=""" & strUpDirs & "catalogo.asp"" -->"
		oTextFile.Close
		Set oTextFile = Nothing
	'End If
	Set oFs = Nothing
End Sub

Sub CrearArchivoProducto(Nombre, NombreAnterior, CodigoProducto, $CodigoCategoria, PathCategoria)
	Set oFs = Server.CreateObject("Scripting.FileSystemObject")
	Dim strFileName
	strFileName = Server.MapPath("..") & PathCategoria & "/" & Nombre
	
	' Si tiene una barra al principio se la quito para que no sume un nivel.
	'If(Left(PathCategoria, Len(PathCategoria) - (Len(PathCategoria)-1)) = "/")Then
	'	PathCategoria = Right(PathCategoria, Len(PathCategoria) - 1)
	'End If

	' Obtengo el NIVEL para ver cuantos directorios arriba esta el archivo de detalle del producto
	Nivel = Ubound(Split(PathCategoria, "/"))
	
	If (NombreAnterior <> "") Then
		strNombreAnteriorArchivo = Server.MapPath("..") & PathCategoria & "/" & NombreAnterior
		If (oFs.FileExists(strNombreAnteriorArchivo)) Then
			oFs.DeleteFile(strNombreAnteriorArchivo)
		End If
	End If
	
	If (Not oFs.FileExists(strFileName)) Then
		strUpDirs = ""
		If (Nivel <= 0) Then 
			Nivel = 1 ' Es el caso de que sea un directorio Raiz
		Else
			Nivel = Nivel
		End If
		For i=1 To Nivel
			strUpDirs = "../" & strUpDirs
		Next

		Set oTextFile = oFs.CreateTextFile(strFileName, true)
		oTextFile.WriteLine APERTURA_SENTENCIA_ASP & "@ Language=VBScript " & CIERRE_SENTENCIA_ASP
		oTextFile.WriteLine APERTURA_SENTENCIA_ASP
		oTextFile.WriteLine "id_categoria=" & $CodigoCategoria
		oTextFile.WriteLine "id_producto=" & CodigoProducto
		oTextFile.WriteLine CIERRE_SENTENCIA_ASP
		oTextFile.WriteLine "<!-- #include file=""" & strUpDirs & "catalogo_detalle.asp"" -->"
		oTextFile.Close
		Set oTextFile = Nothing
	End If
	Set oFs = Nothing
End Sub

Function GetNombreArchivo(Nombre)
	GetNombreArchivo = LCase(Nombre & ".asp")
End Function

Function CrearNombreArchivoProducto(NombreProducto, Fabricante, Modelo)
	strNombre = Fabricante
	If (NombreProducto <> "") Then strNombre = strNombre & "-" & NombreProducto
	If (Modelo <> "") Then strNombre = strNombre & "-" & Modelo
	CrearNombreArchivoProducto = GetNombreArchivo(strNombre)
End Function
%>