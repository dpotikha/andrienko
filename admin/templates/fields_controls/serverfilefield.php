<?php 
$cname = $CM_CONTROL_NAME;
$fname =$CM_PARAMS["filename"];
$fsize =$CM_PARAMS["filesize"];
$furl = $CM_PARAMS["fileurl"];
if(substr_count($CM_BASE_PARAMS['root'],'/{TEMP_USER}/')>0){
	$n=Node::findById($_GET['parent_id']);
	$CM_BASE_PARAMS['root']=str_replace('/{TEMP_USER}/','/secure_files/tempfiles/'.$n->name.'/',$CM_BASE_PARAMS['root']);
	//echo $CM_BASE_PARAMS['root'];
}
//print_r($CM_BASE_PARAMS);
?>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<td><?php if($fname) {?><a href="<?=$furl?>" target="_blank"><?=$AdminTrnsl["Link_to_file"]?></a> (size: <?=$fsize?>&nbsp;b)&nbsp;&nbsp;<?php }?></td>
<td><input type="text" size="40" style="width:245px" value="<?=preg_replace('/\/+/','/',$fname)?>" name="<?=$cname?>"></td>
<td>&nbsp;</td>
<td><input type="button" value="<?=$AdminTrnsl["Select"]?>..." onClick="window.cname='<?=$cname?>';wc=window.open('/admin/filelib.php?cname=<?=$CM_CONTROL_NAME?>&root=<?=$CM_BASE_PARAMS['root']?>', 'wi', 'resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=1000, height=800,scrollbars=yes,fullscreen=no,top=100, left=100');wc.focus()" class=button1>
<?php if ($fname){?><input type="hidden" name="<?=$cname?>_del" value="0"><input type=button onClick="javascript:if(confirm('<?=$AdminTrnsl["Confirm_DeleteFileLink"]?>')){this.form['<?=$cname?>_del'].value='1';alert('<?=$AdminTrnsl["Msg_FileDeletedAfterSave"]?>');this.disabled=true; this.style.display='none'; this.form['<?=$cname?>'].value='';}" value="<?=$AdminTrnsl["Delete_file"]?>" class=button1><?php }?></td>
</table>
