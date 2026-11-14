		var cropper_info={};
		var crop_img_thumb= {x1:0, x2:0, y1:0, y2:0, width:0, height:0};
		var crop_img=       {x1:0, x2:0, y1:0, y2:0, width:0, height:0};
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

		}
		function onCompleteUpload(t)
		{
			var tt=eval(t);
			t='/imglib/_newimage/tmp/'+tt[0].folder+'/end/'+tt[0].img_name;
			cropper_info.folder=tt[0].folder;
			cropper_info.image=tt[0].img_name;
			cropper_info.original_width=tt[0].width;
			cropper_info.original_height=tt[0].height;
			$("cropper_old_path").value=cropper_info.folder;
			$("loading_content").innerHTML='<div id="cropThumbImageArea"><div><strong>Thumbnail Image:</strong></div><div style="clear:both"></div><div style="float:left; margin:10px;"><img src="'+t+'" alt="Crop image"   id="cropImageThumb"/></div><div id="previewImageThumb" style="margin:10px;"></div><div style="clear:both"></div></div><div id="cropImageArea" style="display:none"><div><strong>Image:</strong></div><div style="clear:both"></div><div style="float:left; margin:10px;"><img src="'+t+'" alt="Crop image"   id="cropImage"/></div><div id="previewImage" style="margin:10px;"></div><div style="clear:both"></div></div>';
				$("cropper_nextBtn").style.display="";
				var ratio=cropper_info.width/cropper_info.height;
				var minWidth=(cropper_info.original_width>cropper_info.thumbnail_width)?cropper_info.thumbnail_width:cropper_info.original_width;
				var minHeight=(cropper_info.original_height>cropper_info.thumbnail_height)?cropper_info.thumbnail_height:cropper_info.original_height;
				if(ratio>1&&(cropper_info.original_width<cropper_info.thumbnail_width)){
					minHeight=Math.floor(minWidth/ratio);	
				}else if(ratio<1&&(cropper_info.original_height<cropper_info.thumbnail_height)){
					minWidth=Math.floor(minHeight*ratio);
				}
				
				var ratioDim=(cropper_info.resize_strategy=="crop")?{ x:  cropper_info.thumbnail_width, y: cropper_info.thumbnail_height}:{ x:  cropper_info.original_width, y: cropper_info.original_height};
				if(cropper_info.resize_strategy=="bywidth"||(cropper_info.resize_strategy=="bygreaterside"&&cropper_info.original_width>=cropper_info.original_height)||(cropper_info.resize_strategy=="bysmallerside"&&cropper_info.original_width<=cropper_info.original_height)){
						minHeight=cropper_info.original_height*minWidth/cropper_info.original_width;
					}else if(cropper_info.resize_strategy=="byheight"||(cropper_info.resize_strategy=="bygreaterside"&&cropper_info.original_width<=cropper_info.original_height)||(cropper_info.resize_strategy=="bysmallerside"&&cropper_info.original_width>=cropper_info.original_height)){
						minWidth=cropper_info.original_width*minHeight/cropper_info.original_height;
				}
				cropper_thumb_img=new Cropper.ImgWithPreview( 
					'cropImageThumb',
					{ 
						minWidth: minWidth, 
						minHeight: minHeight, 						
						ratioDim: ratioDim ,
						displayOnInit: true, 
						displayCropOnInit: true, 
						onEndCrop: onEndCropImgThumb,
						previewWrap: 'previewImageThumb'
					} 
				);
				var ratio=cropper_info.width/cropper_info.height;
				var minWidth=(cropper_info.original_width>cropper_info.width)?cropper_info.width:cropper_info.original_width;
				var minHeight=(cropper_info.original_height>cropper_info.height)?cropper_info.height:cropper_info.original_height;
				if(ratio>1&&(cropper_info.original_width<cropper_info.width)){
					minHeight=Math.floor(minWidth/ratio);	
				}else if(ratio<1&&(cropper_info.original_height<cropper_info.height)){
					minWidth=Math.floor(minHeight*ratio);
				}
				var ratioDim=(cropper_info.resize_strategy=="crop")?{ x:  cropper_info.width, y: cropper_info.height}:{ x:  cropper_info.original_width, y: cropper_info.original_height};
				if(cropper_info.resize_strategy=="bywidth"||(cropper_info.resize_strategy=="bygreaterside"&&cropper_info.original_width>=cropper_info.original_height)||(cropper_info.resize_strategy=="bysmallerside"&&cropper_info.original_width<=cropper_info.original_height)){
						minHeight=cropper_info.original_height*minWidth/cropper_info.original_width;
					}else if(cropper_info.resize_strategy=="byheight"||(cropper_info.resize_strategy=="bygreaterside"&&cropper_info.original_width<=cropper_info.original_height)||(cropper_info.resize_strategy=="bysmallerside"&&cropper_info.original_width>=cropper_info.original_height)){
						minWidth=cropper_info.original_width*minHeight/cropper_info.original_height;
				}
				cropper_img=new Cropper.ImgWithPreview( 
					'cropImage',
					{ 
						minWidth: minWidth, 
						minHeight:minHeight , 
						ratioDim: ratioDim,
						displayOnInit: true, 
						displayCropOnInit: true, 
						onEndCrop: onEndCropImg,
						previewWrap: 'previewImage'
					} 
				);
		}
		function fileSubmit(file)
		{
			if(file.value!=""){
				AFU=new ajaxFileUpload();
				AFU.create(file,{"onStart":onStartUpload,"onComplete":onCompleteUpload});
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
			$("popupWindowBodyContent").innerHTML='<div style="margin:10px; margin-bottom:0px; float:left"><form action="/admin/nodes.php" method="post" enctype="multipart/form-data" onSubmit="return fileSubmit(this.file)""><input type="hidden" name="do" value="afu"><input type="file" name="file"  ><input type="hidden" id="cropper_old_path" name="cropper_old_path" value="'+old_path+'"><input type="submit" value="Upload"></form></div><div style="float:left; margin:10px; margin-bottom:0px;"><input type="hidden" id="test_id"></div><div style="margin:10px; margin-bottom:0px;float:right; display:none" id="cropper_doneBtn"><input type="button" value="Done" onClick="cropper_Submit()"></div><div style="margin:10px; margin-bottom:0px;float:right;display:none" id="cropper_backBtn"><input type="button" value="Back" onClick="cropper_backBtnClick()"></div><div style="margin:10px; margin-bottom:0px;float:right;display:none" id="cropper_nextBtn"><input type="button" value="Next" onClick="cropper_nextBtnClick()"></div><div style="clear:both"></div><div id="loading_content" align="center"></div>';	
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
	$("cropImageArea").style.display="";
	$("cropper_nextBtn").style.display="none";
	$("cropper_backBtn").style.display="";
	$("cropper_doneBtn").style.display="";
	
	}
function cropper_backBtnClick()
{
	$("cropThumbImageArea").style.display="";
	$("cropImageArea").style.display="none";
	$("cropper_nextBtn").style.display="";
	$("cropper_backBtn").style.display="none";
	$("cropper_doneBtn").style.display="none";
	}
function cropper_Submit(form){
	var value_img_thumb=crop_img_thumb.x1+":"+crop_img_thumb.x2+":"+crop_img_thumb.y1+":"+crop_img_thumb.y2;
	var value_img=crop_img.x1+":"+crop_img.x2+":"+crop_img.y1+":"+crop_img.y2;
	var res_value=cropper_info.folder+"#"+cropper_info.image+"#"+value_img_thumb+"#"+value_img;
	$("test_id").value=res_value;
	$(cropper_info.control_name).value=res_value;
	popupWindow.hide();
}