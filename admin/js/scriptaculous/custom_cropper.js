var cropper_info={};
var crop_img_thumb= {
    x1:0, 
    x2:0, 
    y1:0, 
    y2:0, 
    width:0, 
    height:0
};
var crop_img2_thumb= {
    x1:0, 
    x2:0, 
    y1:0, 
    y2:0, 
    width:0, 
    height:0
};
var crop_img3_thumb= {
    x1:0, 
    x2:0, 
    y1:0, 
    y2:0, 
    width:0, 
    height:0
};
var crop_img=       {
    x1:0, 
    x2:0, 
    y1:0, 
    y2:0, 
    width:0, 
    height:0
};
var current_crop_step = 1;
var total_crop_steps = 2;
function onCrop(el){
    var form=el.form;
}
function UploadImage(el,options)
{
    if(typeof(popupWindow)!="undefined")
        popupWindow.show(el);
    cropper_info=options;
    cropper_info.ratioX=cropper_info.ratioY=cropper_info.ratioThumbX=cropper_info.ratioThumbY=1;
			
}
function onStartUpload()
{
    $("cropper_nextBtn").style.display="none";
    $("cropper_backBtn").style.display="none";
    $("cropper_doneBtn").style.display="none";
				
    $("cropper_nextBtn2").style.display="none";
    $("cropper_backBtn2").style.display="none";

    $("cropper_nextBtn3").style.display="none";
    $("cropper_backBtn3").style.display="none";
}
function onCompleteUpload(t)
{
    var tt=eval(t);
    t='/s/tmp/'+tt[0].folder+'/end/'+tt[0].img_name;
    cropper_info.folder=tt[0].folder;
    cropper_info.image=tt[0].img_name;
    cropper_info.original_width=tt[0].width;
    cropper_info.original_height=tt[0].height;
    $("cropper_old_path").value=cropper_info.folder;
    $("loading_content").innerHTML='<div id="cropThumbImageArea"><div><strong>Thumbnail Image:</strong></div><div style="clear:both"></div><div style="float:left; margin:10px;"><img src="'+t+'" alt="Crop image"   id="cropImageThumb"/></div><div id="previewImageThumb" style="margin:10px;"></div><div style="clear:both"></div></div>\n\
\n\
<div id="cropThumb2ImageArea" style="display:none"><div><strong>Thumbnail2 Image:</strong></div><div style="clear:both"></div><div style="float:left; margin:10px;"><img src="'+t+'" alt="Crop image"   id="cropImageThumb2"/></div><div id="previewImageThumb2" style="margin:10px;"></div><div style="clear:both"></div></div>\n\
\n\
<div id="cropThumb3ImageArea" style="display:none"><div><strong>Thumbnail3 Image:</strong></div><div style="clear:both"></div><div style="float:left; margin:10px;"><img src="'+t+'" alt="Crop image"   id="cropImageThumb3"/></div><div id="previewImageThumb3" style="margin:10px;"></div><div style="clear:both"></div></div>\n\
\n\
    <div id="cropImageArea" style="display:none"><div><strong>Image:</strong></div><div style="clear:both"></div><div style="float:left; margin:10px;"><img src="'+t+'" alt="Crop image"   id="cropImage"/></div><div id="previewImage" style="margin:10px;"></div><div style="clear:both"></div></div>';
    $("cropper_nextBtn").style.display="";
				
    //********************************************************************************************************
                                
    setupCropArea({
        originalW:cropper_info.original_width,
        originalH:cropper_info.original_height,
        cropW:cropper_info.thumbnail_width,
        cropH:cropper_info.thumbnail_height,
        w:cropper_info.width,
        h:cropper_info.height,
        rs:cropper_info.thumb_resize_strategy,
        ta:cropper_info.thumb_area,
        crop_area:'cropImageThumb',
        onEndCrop:onEndCropImgThumb,
        previewArea:'previewImageThumb'
        
    });
				
    //********************************************************************************************************
                                
    var add_thumbs = new Array();
    add_thumbs = explode("#",cropper_info.thumb_adds);
                              
    if(typeof(add_thumbs[0])!="undefined" && add_thumbs[0]!=""){
        total_crop_steps++;                           
        var p=new Array();
        p=explode(":",add_thumbs[0]);
                                
       
       
        setupCropArea({
            originalW:cropper_info.original_width,
            originalH:cropper_info.original_height,
            cropW:p[0],
            cropH:p[1],
            rs:p[2],
            ta:cropper_info.thumb_area,
            crop_area:'cropImageThumb2',
            onEndCrop:onEndCropImgThumb2,
            previewArea:'previewImageThumb2'

        });
    }       
                                
    //********************************************************************************************************
                                    
    if(typeof(add_thumbs[1])!="undefined" && add_thumbs[1]!=""){
        
        total_crop_steps++;
        
        var p=new Array();
        p=explode(":",add_thumbs[1]);
                                
                                
        setupCropArea({
            originalW:cropper_info.original_width,
            originalH:cropper_info.original_height,
            cropW:p[0],
            cropH:p[1],
            rs:p[2],
            ta:cropper_info.thumb_area,
            crop_area:'cropImageThumb3',
            onEndCrop:onEndCropImgThumb3,
            previewArea:'previewImageThumb3'

        });
    }
    
    //********************************************************************************************************
    
    setupCropArea({
        originalW:cropper_info.original_width,
        originalH:cropper_info.original_height,
        cropW:cropper_info.width,
        cropH:cropper_info.height,
        rs:cropper_info.resize_strategy,
        ta:'maximum',
        crop_area:'cropImage',
        onEndCrop:onEndCropImg,
        previewArea:'previewImage'

    });

    
				
//********************************************************************************************************
                                
}
function fileSubmit(file)
{
    if(file.value!=""){
        AFU=new ajaxFileUpload();
        AFU.create(file,{
            "onStart":onStartUpload,
            "onComplete":onCompleteUpload
        });
    }
    $("loading_content").innerHTML='<img src="/admin/_img/indicator_flower.gif">';
    return false;
}
function afterShow()
{
    var old_path="";
    var crop_value=$(cropper_info.control_name).value;
    var arr=crop_value.split("#");
    if(arr.length==4){
        old_path=arr[0];
    }
    $("popupWindowBodyContent").innerHTML='<div style="margin:10px; margin-bottom:0px; float:left"><form action="/admin/nodes.php" method="post" enctype="multipart/form-data" onSubmit="return fileSubmit(this.file)""><input type="hidden" name="do" value="afu"><input type="file" name="file"  ><input type="hidden" id="cropper_old_path" name="cropper_old_path" value="'+old_path+'"><input type="submit" value="Upload"></form></div><div style="float:left; margin:10px; margin-bottom:0px;"><input type="hidden" id="test_id"></div><div style="margin:10px; margin-bottom:0px;float:right; display:none" id="cropper_doneBtn"><input type="button" value="Done" onClick="cropper_Submit()"></div>\n\
\n\
<div style="margin:10px; margin-bottom:0px;float:right;display:none" id="cropper_backBtn"><input type="button" value="Back" onClick="cropper_backBtnClick()"></div><div style="margin:10px; margin-bottom:0px;float:right;display:none" id="cropper_nextBtn"><input type="button" value="Next" onClick="cropper_nextBtnClick()"></div>\n\
\n\
<div style="margin:10px; margin-bottom:0px;float:right;display:none" id="cropper_nextBtn2"><input type="button" value="Next" onClick="cropper_nextBtn2Click()"></div><div style="margin:10px; margin-bottom:0px;float:right;display:none" id="cropper_backBtn2"><input type="button" value="Back" onClick="cropper_backBtn2Click()"></div>\n\
\n\
<div style="margin:10px; margin-bottom:0px;float:right;display:none" id="cropper_nextBtn3"><input type="button" value="Next" onClick="cropper_nextBtn3Click()"></div><div style="margin:10px; margin-bottom:0px;float:right;display:none" id="cropper_backBtn3"><input type="button" value="Back" onClick="cropper_backBtn3Click()"></div>\n\
\n\
<div style="clear:both"></div><div id="loading_content" align="center"></div>';	
}
var imageid="cropImage";
function onEndCropImgThumb(coords,dimensions) {
    var ratioX=ratioY=1;
    crop_img_thumb.x1=Math.floor(coords.x1*ratioX);
    crop_img_thumb.x2=Math.floor(coords.x2*ratioX);
    crop_img_thumb.y1=Math.floor(coords.y1*ratioY);
    crop_img_thumb.y2=Math.floor(coords.y2*ratioY);
    crop_img_thumb.width=Math.floor(dimensions.width*ratioX);
    crop_img_thumb.height=Math.floor(dimensions.height*ratioY);
}
function onEndCropImgThumb2(coords,dimensions) {
    var ratioX=ratioY=1;
    crop_img2_thumb.x1=Math.floor(coords.x1*ratioX);
    crop_img2_thumb.x2=Math.floor(coords.x2*ratioX);
    crop_img2_thumb.y1=Math.floor(coords.y1*ratioY);
    crop_img2_thumb.y2=Math.floor(coords.y2*ratioY);
    crop_img2_thumb.width=Math.floor(dimensions.width*ratioX);
    crop_img2_thumb.height=Math.floor(dimensions.height*ratioY);
}
function onEndCropImgThumb3(coords,dimensions) {
    var ratioX=ratioY=1;
    crop_img3_thumb.x1=Math.floor(coords.x1*ratioX);
    crop_img3_thumb.x2=Math.floor(coords.x2*ratioX);
    crop_img3_thumb.y1=Math.floor(coords.y1*ratioY);
    crop_img3_thumb.y2=Math.floor(coords.y2*ratioY);
    crop_img3_thumb.width=Math.floor(dimensions.width*ratioX);
    crop_img3_thumb.height=Math.floor(dimensions.height*ratioY);
}
function onEndCropImg(coords,dimensions) {
    var ratioX=ratioY=1;
    crop_img.x1=Math.floor(coords.x1*ratioX);
    crop_img.x2=Math.floor(coords.x2*ratioX);
    crop_img.y1=Math.floor(coords.y1*ratioY);
    crop_img.y2=Math.floor(coords.y2*ratioY);
    crop_img.width=Math.floor(dimensions.width*ratioX);
    crop_img.height=Math.floor(dimensions.height*ratioY);
}
function cropper_nextBtnClick()
{
    $("cropThumbImageArea").style.display="none";
    $("cropper_nextBtn").style.display="none";
    if(total_crop_steps>2){
        $("cropThumb2ImageArea").style.display="";
        $("cropper_backBtn2").style.display="";
        $("cropper_nextBtn2").style.display="";
    }else{
        $("cropImageArea").style.display="";
        $("cropper_backBtn").style.display="";
        $("cropper_doneBtn").style.display="";
    }
	
}
function cropper_nextBtn2Click()
{
    $("cropThumb2ImageArea").style.display="none";
    $("cropper_backBtn2").style.display="none";
    $("cropper_nextBtn2").style.display="none";
    if(total_crop_steps>3){
        $("cropThumb3ImageArea").style.display="";
        $("cropper_backBtn3").style.display="";
        $("cropper_nextBtn3").style.display="";
    }else{
        $("cropImageArea").style.display="";
        $("cropper_backBtn").style.display="";
        $("cropper_doneBtn").style.display="";
         
    }
    
	
}
function cropper_nextBtn3Click()
{
    $("cropThumb3ImageArea").style.display="none";
    $("cropper_backBtn3").style.display="none";
    $("cropper_nextBtn3").style.display="none";
    
    $("cropImageArea").style.display="";
    $("cropper_backBtn").style.display="";
    $("cropper_doneBtn").style.display="";
	
}
function cropper_backBtnClick()
{
    $("cropImageArea").style.display="none";
    $("cropper_backBtn").style.display="none";
    $("cropper_doneBtn").style.display="none";
    if(total_crop_steps>3){
        $("cropThumb3ImageArea").style.display="";
        $("cropper_nextBtn3").style.display="";
        $("cropper_backBtn3").style.display="";
    }else if(total_crop_steps>2){
        $("cropThumb2ImageArea").style.display="";
        $("cropper_nextBtn2").style.display="";
        $("cropper_backBtn2").style.display="";
        
    }else{
        $("cropThumbImageArea").style.display="";
        $("cropper_nextBtn").style.display="";
    }
    
}
function cropper_backBtn2Click()
{
    $("cropThumb2ImageArea").style.display="none";
    $("cropper_backBtn2").style.display="none";
    $("cropper_nextBtn2").style.display="none";
    
    $("cropThumbImageArea").style.display="";
    $("cropper_nextBtn").style.display="";
}
function cropper_backBtn3Click()
{
    
    $("cropThumb3ImageArea").style.display="none";
    $("cropper_backBtn3").style.display="none";
    $("cropper_nextBtn3").style.display="none";
    
    $("cropThumb2ImageArea").style.display="";
    $("cropper_backBtn2").style.display="";
    $("cropper_nextBtn2").style.display="";
}
function cropper_Submit(form){
    var value_img_thumb=crop_img_thumb.x1+":"+crop_img_thumb.x2+":"+crop_img_thumb.y1+":"+crop_img_thumb.y2;
    var value_img_thumb2=crop_img2_thumb.x1+":"+crop_img2_thumb.x2+":"+crop_img2_thumb.y1+":"+crop_img2_thumb.y2;
    var value_img_thumb3=crop_img3_thumb.x1+":"+crop_img3_thumb.x2+":"+crop_img3_thumb.y1+":"+crop_img3_thumb.y2;
	
    var value_img=crop_img.x1+":"+crop_img.x2+":"+crop_img.y1+":"+crop_img.y2;
    var res_value=cropper_info.folder+"#"+cropper_info.image+"#"+value_img_thumb+"#"+value_img;
    res_value=res_value+'#'+value_img_thumb2;
    res_value=res_value+'#'+value_img_thumb3;
    $("test_id").value=res_value;
    $(cropper_info.control_name).value=res_value;
    popupWindow.hide();
}
function setupCropArea(c)
{
    
    var _w=0;
    var _h=0;
    
    var ratio=(c.rs=="crop") ? c.cropW/c.cropH : c.originalW/c.originalH;
    var minWidth=(c.originalW>c.cropW)?c.cropW:c.originalW;
    var minHeight=(c.originalH>c.cropH)?c.cropH:c.originalH;
    if(ratio>1&&(c.originalW<c.cropW)){
        minHeight=Math.floor(minWidth/ratio);	
    }else if(ratio<1&&(c.originalH<c.cropH)){
        minWidth=Math.floor(minHeight*ratio);
    }
				
    var ratioDim=(c.rs=="crop")?{
        x:  c.cropW, 
        y: c.cropH
    }:{
        x:  c.originalW, 
        y: c.originalH
    };
    if(c.rs=="bywidth"||(c.rs=="bygreaterside"&&c.originalW>=c.originalH)||(c.rs=="bysmallerside"&&c.originalW<=c.originalH)){
        minHeight=c.originalH*minWidth/c.originalW;
    }else if(c.rs=="byheight"||(c.rs=="bygreaterside"&&c.originalW<=c.originalH)||(c.rs=="bysmallerside"&&c.originalW>=c.originalH)){
        minWidth=c.originalW*minHeight/c.originalH;
    }
    var dim = ratioDim.x/ratioDim.y;
    if(minWidth/minHeight>1){
        _w=c.originalW;
        _h=_w*minHeight/minWidth;
    }else{
        _h=c.originalH;
        _w=_h*minWidth/minHeight;
    }
    if(c.ta=='maximum'){
        var x1 = Math.floor((c.originalW-_w)/2);
        var y1 = Math.floor((c.originalH-_h)/2);
        var x2 = x1 + _w;
        var y2 = y1 + _h;
    }else{
        var x1 = Math.floor((c.originalW-minWidth)/2);
        var y1 = Math.floor((c.originalH-minHeight)/2);
        var x2 = x1 + minWidth;
        var y2 = y1 + minHeight;
    }
    var crop_config={
        onloadCoords:{
                x1:x1,
                y1:y1,
                x2:x2,
                y2:y2
            },	
            minWidth: minWidth, 
            minHeight: minHeight, 						
            displayOnInit: true, 
            displayCropOnInit: true, 
            onEndCrop: c.onEndCrop,
            previewWrap: c.previewArea
    };
    
    if(true || c.rs=="crop"){
        crop_config.ratioDim=ratioDim;
    }
    var cropper_thumb_img=new Cropper.ImgWithPreview( 
        c.crop_area,
        crop_config
        );
    
}
function explode( delimiter, string ) {	// Split a string by string
    // 
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: kenneth
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

    var emptyArray = {
        0: ''
    };

    if ( arguments.length != 2
        || typeof arguments[0] == 'undefined'
        || typeof arguments[1] == 'undefined' )
        {
        return null;
    }

    if ( delimiter === ''
        || delimiter === false
        || delimiter === null )
        {
        return false;
    }

    if ( typeof delimiter == 'function'
        || typeof delimiter == 'object'
        || typeof string == 'function'
        || typeof string == 'object' )
        {
        return emptyArray;
    }

    if ( delimiter === true ) {
        delimiter = '1';
    }

    return string.toString().split ( delimiter.toString() );
}