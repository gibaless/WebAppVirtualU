<%@ Page Language="C#"%>
<%@ Import Namespace="System.Drawing" %>
<%@ Import Namespace="System.Drawing.Imaging" %>
<% Server.ScriptTimeout = 600000; %>
<script runat="server">
// Smart Image Processor 1.1
// Version: 1.1.2


    /*
    Parámetros:

    p  = Path destino. Ej.: images/productos/
    f  = Nombre del archivo original.
    df = Despues de realizar las copias borra la imagen original. Por default: false.
    q  = Calidad de la imagen. Por default 80.
    
    // Ejemplo de parametros para la imagen.
    nf1 = Nombre del nuevo archivo.
    w1  = Width del nuevo archivo.
    h1  = Height del nuevo archivo.
      
    nf2 = Nombre del nuevo archivo.
    w2  = Width del nuevo archivo.
    h2  = Height del nuevo archivo.

    // Cantidad de imagenes a grabar a partir de una.
    ci = Por default son 1;
    
    // Aspect Radio.
    ar = Por default: false.
    */
    private static String strFileFinal = "";
	private static String strExt = "";


    void Page_Load(Object s, EventArgs e)
    {
        //get image from parameter
        string strPath = Server.MapPath("../") + (Request["p"] != null && Request["p"] != "" ? Request["p"] : "");
        //if (strPath.Substring(strPath.Length - 1, 1) != "\\") strPath += strPath + "\\";

        // Nombre del archivo.
        string strFileOrig = (Request["f"] != null && Request["f"] != "" ? Request["f"] : "no_image.gif");
        
        if (System.IO.File.Exists(strPath + strFileOrig))
        {
            // Cantidad de imagenes a generar a partir de una.
            int CANT_IMG = (Request["ci"] != null && Request["ci"] != "" && int.Parse(Request["ci"]) > 0 ? int.Parse(Request["ci"]) : 1);

            // Aspect Radio.
            Boolean bAspectRadio = (Request["ar"] != null && Request["ar"] != "" ? bool.Parse(Request["ar"]) : false);
            
            // Borrar Imagen Original.
            Boolean bDeleteImageOrig = (Request["df"] != null && Request["df"] != "" ? bool.Parse(Request["df"]) : false);

            string newFileName = "";
            strExt = new System.IO.FileInfo(strPath + strFileOrig).Extension.ToLower();           
            int iWidth = 0;
            int iHeight = 0;
            int iQuality = 80;

            for (int i = 1; i <= CANT_IMG; i++)
            {
                newFileName = (Request["nf" + i] != null && Request["nf" + i] != "" ? Request["nf" + i] : strFileOrig.Substring(0,strFileOrig.Length-4));
                iWidth = (Request["w" + i] != null && Request["w" + i] != string.Empty ? int.Parse(Request["w" + i]) : 0);
                iHeight = (Request["h" + i] != null && Request["h" + i] != string.Empty ? int.Parse(Request["h" + i]) : 0);
                iQuality = (Request["q" + i] != null && Request["q" + i] != string.Empty ? int.Parse(Request["q" + i]) : iQuality);

                if (newFileName != "")
                {
                    strFileFinal = newFileName;
                    /*
					Response.Write("<br />strPath: " + strPath);
                    Response.Write("<br />CANT_IMG: " + CANT_IMG);
                    Response.Write("<br />newFileName: " + newFileName);
                    Response.Write("<br />iWidth: " + iWidth);
                    Response.Write("<br />iHeight: " + iHeight);
                    Response.Write("<br />iQuality: " + iQuality);
                    Response.Write("<br />bAspectRadio: " + bAspectRadio);
                    Response.End();
                    */
					
                    CrearImagen(strPath, strFileOrig, newFileName + strExt, iWidth, iHeight, iQuality, bAspectRadio);
                }
            }

            // Elimino la imagen original.
            if (bDeleteImageOrig)
            {
               System.IO.File.Delete(strPath + strFileOrig);
            }
        }
    }
    
    private static String CrearImagen(String Path, String FileNameOrig, String FileNameNew, int Width, int Height, int Quality, Boolean AspectRadio)
    {
        string strResult = "true";
        
        int intNewWidth, intNewHeight, maxWidth = 10000, maxHeight = 10000, qQuality = 80;
        string pictureFileName = Path + FileNameOrig;
        pictureFileName = pictureFileName.Replace("/", "\\");
        System.Drawing.Image inputImage = System.Drawing.Image.FromFile(pictureFileName);

        if (Width > 0)
            maxWidth = Width;
        else
            maxWidth = inputImage.Width;

        if (Height > 0)
            maxHeight = Height;
        else
            maxHeight = inputImage.Height;

        if (Quality > 0) qQuality = Quality;


        //define size for new image
        if (AspectRadio)
        {
            if (maxWidth < inputImage.Width || maxHeight < inputImage.Height)
            {
                if (maxWidth >= maxHeight)
                {
                    intNewWidth = (int)((double)maxHeight * ((double)inputImage.Width / (double)inputImage.Height));
                    intNewHeight = maxHeight;
                }
                else
                {
                    intNewWidth = maxWidth;
                    intNewHeight = (int)((double)maxWidth * ((double)inputImage.Height / (double)inputImage.Width));
                }
                if (intNewWidth > maxWidth)
                {
                    intNewWidth = maxWidth;
                    intNewHeight = (int)((double)maxWidth * ((double)inputImage.Height / (double)inputImage.Width));
                }
                if (intNewHeight > maxHeight)
                {
                    intNewWidth = (int)((double)maxHeight * ((double)inputImage.Width / (double)inputImage.Height));
                    intNewHeight = maxHeight;
                }
            }
            else
            {
                intNewWidth = inputImage.Width;
                intNewHeight = inputImage.Height;
            }
            /*
            if (maxWidth < inputImage.Width || maxHeight < inputImage.Height) {
                intNewWidth = maxWidth;
                intNewHeight = maxHeight;
                if (inputImage.Width > inputImage.Height)
                    intNewWidth = maxWidth;
                else
                    intNewHeight = maxHeight;
            }else{
                intNewWidth = inputImage.Width;
                intNewHeight = inputImage.Height;
            }
            */
        }
        else
        {
            intNewWidth = maxWidth;
            intNewHeight = maxHeight;
        }
        
        
        System.Drawing.Image outputBitMap = null;
        try
        {
            outputBitMap = new Bitmap(intNewWidth, intNewHeight);
            Graphics g = Graphics.FromImage((System.Drawing.Image)outputBitMap);
            g.InterpolationMode = System.Drawing.Drawing2D.InterpolationMode.HighQualityBicubic;
            g.CompositingQuality = System.Drawing.Drawing2D.CompositingQuality.HighQuality;
            g.DrawImage(inputImage, 0, 0, intNewWidth, intNewHeight);
            g.Dispose();
                        
            System.Drawing.Imaging.ImageFormat formato = System.Drawing.Imaging.ImageFormat.Jpeg;
            if (strExt == ".gif")
                formato = System.Drawing.Imaging.ImageFormat.Gif; //.Jpeg;
            else if (strExt == ".jpg")
                formato = System.Drawing.Imaging.ImageFormat.Jpeg;
            else if (strExt == ".png")
                formato = System.Drawing.Imaging.ImageFormat.Png;


            // Guarda la imagen.
            outputBitMap.Save(Path + FileNameNew, formato);            
        }
        catch (Exception ex)
        {
            //Response.Write("Error: " + ex.Message);
            //Response.End();

            strResult = ex.Message;
        }
        finally
        {
            inputImage.Dispose();
            outputBitMap.Dispose();
        }


        return strResult;
    }


    private static Bitmap ResizeBitmap(Bitmap src, int newWidth, int newHeight)
    {
        Bitmap result = new Bitmap(newWidth, newHeight);
        using (Graphics g = Graphics.FromImage((System.Drawing.Image)result))
        {
            g.DrawImage(src, 0, 0, newWidth, newHeight);
        }
        
        return result;
    }


    
  private static ImageCodecInfo GetEncoderInfo(String mimeType) {
    int j;
    ImageCodecInfo[] encoders;
    encoders = ImageCodecInfo.GetImageEncoders();
    for(j = 0; j < encoders.Length; ++j) {
      if(encoders[j].MimeType == mimeType)
        return encoders[j];
    }
    return null;
  }
    
</script>
<html>
<script language="javascript">
function acceptPathAndClose(formname,field, path,fieldBig,pathBig) {
	form_h = eval("opener.document.forms['" + formname + "']." + field);
	form_h.value = path;

	if (fieldBig != null && fieldBig != '') {
		form_hb = eval("opener.document.forms['" + formname + "']." + fieldBig);
		form_hb.value = pathBig;
	}

	self.close();
}
</script>
<body>
<script language="javascript">
    acceptPathAndClose('<%=Request["frm"]%>', '<%=Request["campo"]%>', '<%=strFileFinal + strExt%>', '<%=Request["campoBig"]%>', '<%=Request["nf2"] + strExt%>');
</script>
</body>
</html>