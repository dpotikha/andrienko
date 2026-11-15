<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html;charset=utf-8">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<link rel=stylesheet href=/admin/css.css type="text/css">
	<title><?=$AdminTrnsl["ImgLibEditImage"]?></title>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top4>
<td height=40 id=pad18 colspan=2 class=head><b><?=$AdminTrnsl["ImgLib"]?> : </b><?=$AdminTrnsl["ImgLibEditImage"]?></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top42><td><img src=/admin/_img/s.gif width=1 height=5></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<td id=pad18 class=h4>
<br>

<table border=0 cellpadding=0 cellspacing=0><td><b><a href="/admin/imglib.php?cdir=&cname=<?=$cname?>"><?=$AdminTrnsl["ImgLibMainFolder"]?></a> :</b><?php  $cd=array();$ds=explode('/', $cdir->getRelPath()); foreach($ds as $d) { if(!$d) continue; $cd[]=$d;?><b> <a href="/admin/imglib.php?cdir=<?=implode('/', $cd)?>&cname=<?=$cname?>"><?=$d?></a> :</b><?php }?> <?=$image->getName()?></td></table>
<br>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back3>
<td class=td2 style="padding:0 0 0 0" align=center><img src="/s/ithumb<?=$image->getUrl()?>" border=0 class=bord1></td>
<td class=td2 style="border:0; padding:0 0 0 0">

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3>
<form action="/admin/imglib.php" method=POST enctype="multipart/form-data">
<input type="hidden" name="do" value="image_update" />
<input type="hidden" name="cname" value="<?=$cname?>" />
<input type="hidden" name="cdir" value="<?=$cdir->getRelPath()?>" />
<input type="hidden" name="fname" value="<?=$image->getName()?>" />
<tr id=back2>
<td><b><?=$AdminTrnsl["ImgLibReplaceFile"]?>:</b></td>
<td class=td2 id=back3>
<?php if($file_error){?><div class=errmsg><?=$file_error?></div><?php }?>
<input type="file" size="45" name="file" style="width:200px"></td>
</tr>
<tr id=back2>
<td><b><?=$AdminTrnsl["ImgLibResizeStrategy"]?>:</b></td>
<td class=td2 id=back3>
<select name="resize_strategy">
	<option value="bywidth"<?php if("bywidth" == $request->getParameter("resize_strategy")){?> SELECTED<?php }?>><?=$AdminTrnsl["ImgLibResizeStrategy_BYWIDTH"]?></option>
	<option value="bygreaterside"<?php if("bygreaterside" == $request->getParameter("resize_strategy")){?> SELECTED<?php }?>><?=$AdminTrnsl["ImgLibResizeStrategy_BYGREATERSIDE"]?></option>
</select></td>
</tr>
<tr id=back2>
<td>&nbsp;</td>
<td class=td2 id=pad10><input type=submit value="<?=$AdminTrnsl["Submit_changes"]?>" class=button5 name="submit"></td>
</tr>
</form>
</table>

</td>
</tr></table>

</td>
</tr></table>

</body>
</html>
