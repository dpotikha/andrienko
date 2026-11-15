<?php 
	$cname = $CM_CONTROL_NAME;
//	print_r($CM_PARAMS);
?>
   <link href="/admin/js/scriptaculous/animPopupWindow.css" rel="stylesheet" type="text/css"/>
   <link href="/admin/js/scriptaculous/cropper.css" rel="stylesheet" type="text/css" />
  <style type="text/css">
		#previewImageThumb,#previewImage{
			float: left;
			border:1px solid #000000;
		}
	</style>
  <script type="text/javascript" src="/admin/js/scriptaculous/prototype.js"></script> 
  <script type="text/javascript" src="/admin/js/scriptaculous/scriptaculous.js?load=effects,dragdrop,builder"></script>
  <script language="JavaScript" src="/admin/js/scriptaculous/ajax.js"></script> 
  <script language="JavaScript" src="/admin/js/scriptaculous/animPopupWindow.js"></script> 
  <script language="JavaScript" src="/admin/js/scriptaculous/ajaxFileUpload.js"></script> 
  <script language="JavaScript" src="/admin/js/scriptaculous/element_info.js"></script> 
  <script language="JavaScript" src="/admin/js/scriptaculous/move_and_resize.js"></script> 
  <script language="JavaScript" src="/admin/js/scriptaculous/cropper.js"></script> 
  <script language="JavaScript" src="/admin/js/scriptaculous/custom_cropper.js"></script> 
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<tr valign=top>
<td id=pad10r nowrap>

<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<tr>
<td colspan=2><a href="javascript:void(0)" onclick="javascript:UploadImage(this,{control_name:'<?=$CM_CONTROL_NAME?>',relurl:'<?=$CM_PARAMS["relurl"]?>',width:<?=$CM_BASE_PARAMS["width"]?>,height:<?=$CM_BASE_PARAMS["height"]?>,thumbnail_width:<?=$CM_BASE_PARAMS["thumbnail_width"]?>,thumbnail_height:<?=$CM_BASE_PARAMS["thumbnail_height"]?>,resize_strategy:'<?=$CM_BASE_PARAMS["resize_strategy"]?>',thumb_resize_strategy:'<?=$CM_BASE_PARAMS["thumb_resize_strategy"]?>',thumb_area:'<?=$CM_BASE_PARAMS["thumb_area"]?>',thumb_adds:'<?=$CM_BASE_PARAMS["thumb_adds"]?>'})">Upload Image</a></td>
</tr>
<input type="hidden" id="<?=$CM_CONTROL_NAME?>"  name="<?=$CM_CONTROL_NAME?>" value="<?=(isset($CM_PARAMS["base_url"]))?$CM_PARAMS["base_url"]:$CM_PARAMS["relurl"]?>">
<!--<input type="hidden" value="<?=$CM_PARAMS["relurl"]?>" name="<?=$CM_CONTROL_NAME?>">-->
</table>
</td>
<td id=back2><img src=/admin/_img/s.gif width=1 height=1></td>
<?php if ($CM_PARAMS["url"]){?>
<td id=pad10r><a href="<?=$CM_PARAMS["url"]?>" target=_blank><img src="<?=$CM_PARAMS["thumburl"]?>" border=0 alt="<?=$AdminTrnsl["View_Image"]?>"></a></td>
<td id=pad10r><input type="checkbox" name="<?=$cname?>_del" value="1"><span class=errmsg><?=$AdminTrnsl["Delete_file"]?></span></td>
<?php }?>
</tr>
</table>
	<script language="javascript">
	Event.observe(window, 'load', cLoad);
	function cLoad(){
		if(!$("popupElement")){
			popupWindow=new animatePopupWindow();
			popupWindow.init({"width":670,"height":480,"title":"Image manager","afterShow":afterShow});
		}
	}

	</script>
