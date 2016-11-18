<%@ Page Language="C#"%>
<%@ Import Namespace="System.Drawing" %>
<%@ Import Namespace="System.Drawing.Imaging" %>
<script runat="server">
    // Resize.
    // Version: 1.0.1

	void Page_Load(Object s, EventArgs e) {
		int intNewWidth,intNewHeight, maxWidth = 10000, maxHeight = 10000, qQuality = 80;
    	
        string strPath = Server.MapPath("../");
        if (strPath.Substring(strPath.Length - 1, 1) != "\\") strPath += "\\";
		string strF = Request["f"];
		string pictureFileName = strPath + strF;
		pictureFileName = pictureFileName.Replace("/", "\\");
		string newFileName = Request["nf"];
		string newFileNameExtension;

        if (pictureFileName == null || pictureFileName == "" || !System.IO.File.Exists(pictureFileName)) {
		  ////Response.Write("Error: File (" + pictureFileName + ") not found or empty");  
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
		} else {
				intNewWidth = maxWidth;
				intNewHeight = maxHeight;
		}

        System.Drawing.Image outputBitMap=null;
    try {
        outputBitMap = new Bitmap(intNewWidth, intNewHeight);
        Graphics g = Graphics.FromImage((System.Drawing.Image)outputBitMap);
        g.InterpolationMode = System.Drawing.Drawing2D.InterpolationMode.HighQualityBicubic;
        g.CompositingQuality = System.Drawing.Drawing2D.CompositingQuality.HighQuality;
        g.DrawImage(inputImage, 0, 0, intNewWidth, intNewHeight);
        
        g.Dispose();
                        
        System.Drawing.Imaging.ImageFormat formato = System.Drawing.Imaging.ImageFormat.Jpeg;
        if (newFileNameExtension == ".gif")
			formato = System.Drawing.Imaging.ImageFormat.Jpeg;
		else if (newFileNameExtension == ".jpg")
			formato = System.Drawing.Imaging.ImageFormat.Jpeg;
		else if (newFileNameExtension == ".png")
			formato = System.Drawing.Imaging.ImageFormat.Png;
        
        outputBitMap.Save(Response.OutputStream, formato);
    }		
    catch (Exception ex) {
			return;
    }  
    finally
    {
      	inputImage.Dispose();
		outputBitMap.Dispose();  
    }
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