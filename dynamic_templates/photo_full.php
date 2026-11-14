<?php  $img =& $CurrentNode->tfields["photo"] ?>
<html>
	<head>
		<title><?php  $CurrentNode->name ?></title>
		<script language="JavaScript" type="text/javascript">
			var w = <?= $img['width'] ?>, h = <?= $img['height'] ?>;
			window.resizeTo(w+30, h+100);
			window.moveTo((screen.availWidth - w)/2, (screen.availHeight - h)/2 );
		</script>
		<link rel="stylesheet" type="text/css" href="/main.css" >
	</head>
	
	<body style="margin:0px;padding:0px;text-align:center">
		<div class="shadowImgDetailTL_01"><div class="shadowImgDetailBR_01">
			<a href="Закрыть окно" onClick="window.close(); return false;">
				<img src="<?= $img['url'] ?>" name="img" id="img" width="<?= $img['width'] ?>" height="<?= $img['height'] ?>" alt="" border="0" style="display:block"/>
			</a>
			<div style="padding-top:9px">
				<a href="Закрыть окно" onClick="window.close(); return false" style="font-size:10px; color:#333">Закрыть окно</a>
			</div>
		</div></div>
	</body>

</html>
