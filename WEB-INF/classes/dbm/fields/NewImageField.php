<?php 

require_once ("dbm/fields/StringField.php");
//require_once ("dbm/ImgLib.php");
require_once ("imageclass.php");

class NewImageField extends Field {
	var $dbColumnName = "value_str";

	var $tmpFilePath = "";
	var $tmpFileName = "";
	var $deleteOnStore = false;

	var $oldValue = "";

	function NewImageField() {}

	function crop($exp,$fieldDef,$is_thumb=true){
	
			$tmp_dir="/_newimage/tmp/".$exp[0]."/finish/";
			if($is_thumb){
					$img_thumb_exp=explode(":",$exp[2]);
					foreach($img_thumb_exp as $k=>$v){
						if($v<0)$v=0;
						$img_thumb_exp[$k]=abs(ceil($v));
					}
					$cropHeight=$img_thumb_exp[3]-$img_thumb_exp[2];
					$cropWidth=$img_thumb_exp[1]-$img_thumb_exp[0];
					$height=$fieldDef->fieldParameters["thumbnail_height"];
					$width=$fieldDef->fieldParameters["thumbnail_width"];
					$rs=$fieldDef->fieldParameters["thumb_resize_strategy"];
					if($height>$cropHeight||$width>$cropWidth){
						$height=$cropHeight;
						$width=$cropWidth;
					}
					/*if($cropHeight*$width>$cropWidth*$height){
						$cropHeight=$cropWidth*$height/$width;
					}elseif($cropHeight*$width<$cropWidth*$height){
						$cropWidth=$cropHeight*$width/$height;
					}*/
					list($i_width, $i_height, $i_type, $i_attr) = getimagesize(FIELD_IMGLIB_DIR."/_newimage/tmp/".$exp[0]."/end/".$exp[1]);
					if($height>=$i_height&&$width>=$i_width){
						$img = new imageClass();
						$img->setSourceFile(FIELD_IMGLIB_DIR."/_newimage/tmp/".$exp[0]."/end/".$exp[1]);
						$img->setTargetFile(FIELD_IMGLIB_THUMBNAILS_DIR.$tmp_dir.$exp[1]);
						copy(FIELD_IMGLIB_DIR."/_newimage/tmp/".$exp[0]."/end/".$exp[1],FIELD_IMGLIB_THUMBNAILS_DIR.$tmp_dir.$exp[1]);
						$img->setSourceFile(FIELD_IMGLIB_THUMBNAILS_DIR.$tmp_dir.$exp[1]);
						$img->setTargetFile(FIELD_IMGLIB_THUMBNAILS_DIR.$tmp_dir.$exp[1]);
					}else{
						//echo $cropHeight*$width." ".$cropWidth*$height;
						//exit();
						$img = new imageClass();
						$img->setSourceFile(FIELD_IMGLIB_DIR."/_newimage/tmp/".$exp[0]."/end/".$exp[1]);
						$img->setTargetFile(FIELD_IMGLIB_THUMBNAILS_DIR.$tmp_dir.$exp[1]);
						$img->setJpegOutputQuality(100);	
						$img->crop($img_thumb_exp[0], $img_thumb_exp[2],$img_thumb_exp[0]+$cropWidth,$img_thumb_exp[2]+$cropHeight);
			
						$img->setSourceFile(FIELD_IMGLIB_THUMBNAILS_DIR.$tmp_dir.$exp[1]);
						$img->setTargetFile(FIELD_IMGLIB_THUMBNAILS_DIR.$tmp_dir.$exp[1]);
						$img->setJpegOutputQuality(90);	
						if($rs=="bygreaterside"){
							if($cropHeight>$cropWidth){
								$img->resizeToHeight($height);
								$width=$cropWidth*$height/$cropHeight;
							}else{
								$img->resizeToWidth($width);
								$height=$cropHeight*$width/$cropWidth;
							}
						}elseif($rs=="bysmallerside"){
							if($cropWidth>$cropHeight){
								$img->resizeToHeight($height);
								$width=$cropWidth*$height/$cropHeight;
							}else{
								$img->resizeToWidth($width);
								$height=$cropHeight*$width/$cropWidth;
							}
						}elseif($rs=="byheight"){
								$img->resizeToHeight($height);
								$width=$cropWidth*$height/$cropHeight;
						}elseif($rs=="bywidth"){
								$img->resizeToWidth($width);
								$height=$cropHeight*$width/$cropWidth;
						}elseif($rs=="crop"){
								$img->resizeToWidthAndHeight($width,$height);
						}
					}
					//$img->setJpegOutputQuality(90);	
			}else{

					$img_exp=explode(":",$exp[3]);
					foreach($img_exp as $k=>$v){
						if($v<0)$v=0;
						$img_exp[$k]=abs(ceil($v));
					}
					$cropHeight=$img_exp[3]-$img_exp[2];
					$cropWidth=$img_exp[1]-$img_exp[0];
					$height=$fieldDef->fieldParameters["height"];
					$width=$fieldDef->fieldParameters["width"];
					$rs=$fieldDef->fieldParameters["resize_strategy"];
					
					/*if($height>$cropHeight||$width>$cropWidth){
						$height=$cropHeight;
						$width=$cropWidth;
					}*/
					list($i_width, $i_height, $i_type, $i_attr) = getimagesize(FIELD_IMGLIB_DIR."/_newimage/tmp/".$exp[0]."/end/".$exp[1]);
					if($height>=$i_height&&$width>=$i_width){
						$img = new imageClass();
						$img->setSourceFile(FIELD_IMGLIB_DIR."/_newimage/tmp/".$exp[0]."/end/".$exp[1]);
						$img->setTargetFile(FIELD_IMGLIB_DIR.$tmp_dir.$exp[1]);
						copy(FIELD_IMGLIB_DIR."/_newimage/tmp/".$exp[0]."/end/".$exp[1],FIELD_IMGLIB_DIR.$tmp_dir.$exp[1]);
						$img->setSourceFile(FIELD_IMGLIB_DIR.$tmp_dir.$exp[1]);
						$img->setTargetFile(FIELD_IMGLIB_DIR.$tmp_dir.$exp[1]);
					}else{
						$img = new imageClass();
						$img->setSourceFile(FIELD_IMGLIB_DIR."/_newimage/tmp/".$exp[0]."/end/".$exp[1]);
						$img->setTargetFile(FIELD_IMGLIB_DIR.$tmp_dir.$exp[1]);
						$img->setJpegOutputQuality(100);	
						$img->crop($img_exp[0], $img_exp[2],$img_exp[0]+$cropWidth,$img_exp[2]+$cropHeight);
						$img->setSourceFile(FIELD_IMGLIB_DIR.$tmp_dir.$exp[1]);
						$img->setTargetFile(FIELD_IMGLIB_DIR.$tmp_dir.$exp[1]);
						$img->setJpegOutputQuality(90);	
						if($rs=="bygreaterside"){
							if($cropHeight>$cropWidth){
								$img->resizeToHeight($height);
								$width=$cropWidth*$height/$cropHeight;
							}else{
								$img->resizeToWidth($width);
								$height=$cropHeight*$width/$cropWidth;
							}
						}elseif($rs=="bysmallerside"){
							if($cropWidth>$cropHeight){
								$img->resizeToHeight($height);
								$width=$cropWidth*$height/$cropHeight;
							}else{
								$img->resizeToWidth($width);
								$height=$cropHeight*$width/$cropWidth;
							}
						}elseif($rs=="byheight"){
								$img->resizeToHeight($height);
								$width=$cropWidth*$height/$cropHeight;
						}elseif($rs=="bywidth"){
								$img->resizeToWidth($width);
								$height=$cropHeight*$width/$cropWidth;
						}elseif($rs=="crop"){
								$img->resizeToWidthAndHeight($width,$height);
						}
						//$img->setJpegOutputQuality(90);	
					}
			}
			$obj=new PhpObject();
			$obj->img=$img;
			$obj->width=$width;
			$obj->height=$height;
			$obj->tmp_dir="/_newimage/tmp/".$exp[0]."/finish/";
			return $obj;



			
	}
	function setValue($o) {
		global $AdminTrnsl;
		$del = NULL;
		if (is_object($o) && strtolower(get_class($o)) == 'httprequest') {
			$request = $o;
			
			$fieldDef = $this->getFieldDef();
			$rValue = $o->getParameter($fieldDef->shortname);
			$del = (int)$request->getParameter($fieldDef->shortname . "_del");
			$file = $request->getParameter($fieldDef->shortname . "_file");
			$this->tmpFileName = $file->name;
			$this->tmpFilePath = $file->tmp_name;
		} elseif(is_string($o)) {
			$rValue = $o;
		}
			if($del){
				$this->remove();
				$this->value ="";
			}else{
					
					$resizeStrategy=$fieldDef->fieldParameters["resize_strategy"];
					$thumb_resizeStrategy=$fieldDef->fieldParameters["resize_strategy"];
					$exp=explode("#",$rValue);
					if(count($exp)>2){	
						$this->remove();
						$img_obj=$this->crop($exp,$fieldDef,true);
						$img_obj=$this->crop($exp,$fieldDef,false);
						$this->value = $img_obj->tmp_dir.$exp[1];
					}else{
							$this ->value=$rValue;
					}

				
		}
		return;
	}

	function create() {
		$this->update();
	}

	function update() {
		if (strpos($this->value, "/tmp/") !== false) {
			$node=Node::findById($this->nodeId);
			$fieldDef = $this->getFieldDef();
			directoryClass::createDirIfNotExists("/_newimage".$node->getHref()."/",array(FIELD_IMGLIB_DIR,FIELD_IMGLIB_THUMBNAILS_DIR));
			if($fieldDef->fieldParameters["save_original"]==1){
				directoryClass::createDirIfNotExists("/_newimage".$node->getHref()."/",array(FIELD_IMGLIB_ORIGINAL_DIR));
			}
			$path_parts = pathinfo(FIELD_IMGLIB_DIR.$this->value);
			@copy(FIELD_IMGLIB_DIR.$this->value,FIELD_IMGLIB_DIR."/_newimage".$node->getHref()."/".$path_parts["basename"]);
			@copy(FIELD_IMGLIB_THUMBNAILS_DIR.$this->value,FIELD_IMGLIB_THUMBNAILS_DIR."/_newimage".$node->getHref()."/".$path_parts["basename"]);
			if($fieldDef->fieldParameters["save_original"]==1){
				@copy(str_replace("/finish","/start",FIELD_IMGLIB_DIR.$this->value),FIELD_IMGLIB_ORIGINAL_DIR."/_newimage".$node->getHref()."/".$path_parts["basename"]);
			}
			$dirs=explode("/tmp/",$path_parts["dirname"]);
			$dirs1=explode("/",$dirs[1]);
			directoryClass::rmdir(FIELD_IMGLIB_DIR."/_newimage/tmp/".$dirs1[0]);
			directoryClass::rmdir(FIELD_IMGLIB_THUMBNAILS_DIR."/_newimage/tmp/".$dirs1[0]);
			directoryClass::rmdir(FIELD_IMGLIB_ORIGINAL_DIR."/_newimage/tmp/".$dirs1[0]);
			$this->value = "/_newimage".$node->getHref()."/".$path_parts["basename"];
		}
	}

	function remove($val="") {
		if(!strlen($val))$val=$this->value;
		if(file_exists(FIELD_IMGLIB_DIR.$val))
		@unlink(FIELD_IMGLIB_DIR.$val);
		if(file_exists(FIELD_IMGLIB_THUMBNAILS_DIR.$val))
		@unlink(FIELD_IMGLIB_THUMBNAILS_DIR.$val);
		if(file_exists(FIELD_IMGLIB_ORIGINAL_DIR.$val))
		@unlink(FIELD_IMGLIB_ORIGINAL_DIR.$val);
		if(substr_count($val,"/tmp/")>0){
			$exp=explode("/tmp/",$val);
			$exp1=explode("/",$exp[1]);
			directoryClass::rmdir(FIELD_IMGLIB_DIR."/_newimage/tmp/".$exp1[0]);
			directoryClass::rmdir(FIELD_IMGLIB_THUMBNAILS_DIR."/_newimage/tmp/".$exp1[0]);
			directoryClass::rmdir(FIELD_IMGLIB_ORIGINAL_DIR."/_newimage/tmp/".$exp1[0]);
		}
		return true;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();

		$fieldDef = $this->getFieldDef();
		$fieldType = $fieldDef->getFieldType();
		$classParams = $fieldType->getParametersNames();

		foreach ($classParams as $fieldParameterShortname) {
			$params[$fieldParameterShortname] = $fieldDef->getParameterValue($fieldParameterShortname);
		}

		$size_tag = "";
		$url = $this->value;
		if ($url != NULL && strlen($url)) {
			$img = new PhpObject();
			if (file_exists(FIELD_IMGLIB_DIR.$url)) {
					list($img->getWidth, $img->getHeight) =getimagesize(FIELD_IMGLIB_DIR.$url);
					list($img->getThumbnailWidth, $img->getThumbnailHeight) =getimagesize(FIELD_IMGLIB_THUMBNAILS_DIR.$url);
					$img->getFileSize=filesize(FIELD_IMGLIB_DIR.$url);
					$img->getUrl=$url;
			
					$params["size_tag"] = " width=" . $img->getWidth . " height=" . $img->getHeight;
					$params["width"] = $img->getWidth;
					$params["height"] = $img->getHeight;
					$params["url"] = "/imglib" . $img->getUrl;
					$params["relurl"] = $img->getUrl;
	
					$params["thumburl"] = "/imglib_thumbnails" . $img->getUrl;
					$params["thumbwidth"] = $img->getThumbnailWidth;
					$params["thumbheight"] = $img->getThumbnailHeight;
	
					$fsize = $img->getFileSize;
					$params["size"] = $fsize;
					$params["sizeKB"] = round($fsize / 1024);
					$params["sizeMB"] = round($fsize / 1024 / 1024);
			} else {
				$params["size_tag"] = "";
				$params["width"] = -1;
				$params["height"] = -1;
				$params["url"] = "";
				$params["relurl"] = "";

				$params["thumburl"] = "";
				$params["thumbwidth"] = -1;
				$params["thumbheight"] = -1;

				$fsize = -1;
				$params["size"] = -1;
				$params["sizeKB"] = -1;
				$params["sizeMB"] = -1;
			}
		}

		return $params;
	}

	function getValueForTemplate() {
		return $this->getParamersForIncludeControlJSP();
	}

	function getValue() {
		$url = $this->value;
		if ($url != null && strlen($url) != 0) {
			$img = ImgLibImage::findByUrl($url);
			if ($img != null) {
				return "/imglib" . $img->getUrl();
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	function getHtmlVisualValue() {
		return ($this->value) ? "<img src=\"/imglib_thumbnails/".$this->value."\" border=0>" : "&nbsp;";
	}

	function getJSPControlName() {
		$fieldDef = $this->getFieldDef();
		$ctype = $fieldDef->getParameterValue("control_type");
		if ($ctype == null || $ctype=="") {
			$ctype = "normal";
		}

		$class = strtolower(get_class($this));
		$cname = $class . "_" . $ctype;
		return $cname;
	}

	function getDefaultValue($fd) {
		$value = "";
		return $value;
	}
}

?>