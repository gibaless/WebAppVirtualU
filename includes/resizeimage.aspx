<%@ Page Language="C#"%>
<%@ Import Namespace="System.Drawing" %>
<%@ Import Namespace="System.Drawing.Imaging" %>
<script runat="server">
// Smart Image Processor 1.1
// Version: 1.1.2

	void Page_Load(Object s, EventArgs e) {
		//Response.Buffer = true;
		int intNewWidth,intNewHeight, maxWidth = 10000, maxHeight = 10000, qQuality = 80;
    	
		//get image from parameter
		string strPath = Server.MapPath("../");
		if (strPath.Substring(strPath.Length-1,1) != "\\") strPath += strPath + "\\";
		string strF = (Request["f"] != "images/productos/" ? Request["f"] : "images/productos/no_image.gif");
		string pictureFileName = strPath + strF;
		pictureFileName = pictureFileName.Replace("/", "\\");
		//Response.Write(pictureFileName);
		string newFileName = Request["nf"];
		string newFileNameExtension;
		if (pictureFileName == null || pictureFileName == "" || !System.IO.File.Exists(pictureFileName)) {
		  ///////Response.Write("Error: File (" + pictureFileName + ") not found or empty");  
		  return;
		}
    
		newFileNameExtension = new System.IO.FileInfo(pictureFileName).Extension.ToLower();
		newFileName += newFileNameExtension;
		System.Drawing.Image inputImage = System.Drawing.Image.FromFile(pictureFileName);
    
		if ( Request["w"] != null && int.Parse(Request["w"]) > 0 )
			maxWidth = int.Parse(Request["w"]);
		else
			maxWidth = inputImage.Width;
		
		if ( Request["h"] != null && int.Parse(Request["h"]) > 0 ) 
			maxHeight = int.Parse(Request["h"]);
		else
			maxHeight = inputImage.Height;
		
		if ( Request["q"] != null && int.Parse(Request["q"]) > 0 ) qQuality = int.Parse(Request["q"]);
	    
	    //define size for new image
		string aspect = Request["a"];
		if (aspect == "true") {
			if (maxWidth < inputImage.Width || maxHeight < inputImage.Height) {
				if (maxWidth >= maxHeight) {
					intNewWidth = (int)((double)maxHeight*((double)inputImage.Width/(double)inputImage.Height));
					intNewHeight = maxHeight;
				} else {
					intNewWidth = maxWidth;
					intNewHeight = (int)((double)maxWidth*((double)inputImage.Height/(double)inputImage.Width));
				}
				if (intNewWidth > maxWidth) {
					intNewWidth = maxWidth;
					intNewHeight = (int)((double)maxWidth*((double)inputImage.Height/(double)inputImage.Width));
				}
				if (intNewHeight > maxHeight) {
					intNewWidth = (int)((double)maxHeight*((double)inputImage.Width/(double)inputImage.Height));
					intNewHeight = maxHeight;
				}
			} else {
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
		} else {
				intNewWidth = maxWidth;
				intNewHeight = maxHeight;
		}
	/////Response.Write(maxWidth + "x" + maxHeight);
	System.Drawing.Image outputBitMap=null;
    try {        
      //output new image with different size
  		//outputBitMap = new Bitmap(inputImage,intNewWidth,intNewHeight);
  		outputBitMap = inputImage.GetThumbnailImage(intNewWidth,intNewHeight, null, System.IntPtr.Zero);

		/******
     	EncoderParameters eps = new System.Drawing.Imaging.EncoderParameters(1);
     	eps.Param[0] = new System.Drawing.Imaging.EncoderParameter( System.Drawing.Imaging.Encoder.Quality, qQuality );
     	ImageCodecInfo ici = GetEncoderInfo("image/jpeg");
      	if (pictureFileName.ToLower() == newFileName.ToLower())
        	System.IO.File.Delete(pictureFileName);
		********/
     	//outputBitMap.Save( newFileName, ici, eps );    
		////////////outputBitMap.Save( newFileName);  
		System.Drawing.Imaging.ImageFormat formato;
		
		formato = System.Drawing.Imaging.ImageFormat.Jpeg;
		if (newFileNameExtension == ".gif")
			formato = System.Drawing.Imaging.ImageFormat.Jpeg;
		else if (newFileNameExtension == ".jpg")
			formato = System.Drawing.Imaging.ImageFormat.Jpeg;
		else if (newFileNameExtension == ".png")
			formato = System.Drawing.Imaging.ImageFormat.Png;

		//Response.Write(formato.ToString());
		//outputBitMap.Save(Response.OutputStream, ici, eps);
		outputBitMap.Save(Response.OutputStream, formato);
    }		
    catch (Exception ex) {
    	//Response.Write("Error: " + ex.Message);
			return;
    }  
    finally
    {
      	inputImage.Dispose();
		outputBitMap.Dispose();  
    }
    
    //Response.Write("<br>Resize DONE. " + maxWidth + "x" + maxHeight);
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