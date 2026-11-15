<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html;charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<link rel=stylesheet href=/admin/css.css type="text/css">
	<title><?=$AdminTrnsl["ImgLibRenamingfolder"]?></title>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top4>
<td height=40 id=pad18 colspan=2 class=head><b><?=$AdminTrnsl["ImgLib"]?></b> : <?=$AdminTrnsl["ImgLibRenamingfolder"]?></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top42><td><img src=/admin/_img/s.gif width=1 height=5></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<td id=pad18 class=h4>
<br>
<b><a href="/admin/imglib.php?cname=<?=$cname?>&cdir="><?=$AdminTrnsl["ImgLibMainFolder"]?></a></b><?php  $cd=array();$ds=explode('/', $dir->getRelPath());  foreach($ds as $d) { $cd[]=$d;?><b> : <a href="/admin/imglib.php?cdir=<?=implode('/', $cd)?>&cname=<?=$cname?>"><?=$d?></a></b><?php }?><br>
<br>
<script>function checkFName(fname) {
	if (fname == '') {
		alert('<?=$AdminTrnsl["ImgLib Folder Name Required"]?>');
		return false;
	}
	if (!fname.match(/^[A-Za-z0-9_\-\. ]+$/)) {
		alert('<?=$AdminTrnsl["Illegal Char in Name"]?>');
		return false;
	}
	return true;
}</script>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3>
<form action="/admin/imglib.php" method=POST onSubmit="return checkFName(this.dname.value)">
<input type="hidden" name="do" value="folder_rename" />
<input type="hidden" name="cname" value="<?=$cname?>" />
<input type="hidden" name="cdir" value="<?=$cdir->getRelPath()?>" />
<input type="hidden" name="olddname" value="<?=$olddname?>" />
<tr id=back2>
<td class=td2><?=$AdminTrnsl["ImgLibFolderName"]?></td>
<td id=back3><input type="text" name="dname" value="<?=prepareStringForHtml($dname)?>"></td>
</tr>
<tr id=back2>
<td>&nbsp;</td>
<td id=pad10><input type="submit" name="Submit7" value="<?=$AdminTrnsl["ImgLibMenuRename"]?>" class=button5></td>
</tr>
</form>
</table>

</td>
</tr></table>

</body>
</html>
