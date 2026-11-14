<?php  tinclude("_head") ?>
<?php 
$videoFiles = $CurrentNode->getChildrenOfClass("video");
$audioFiles = $CurrentNode->getChildrenOfClass("audio");
?>
<?= $CurrentNode->tfields["text"]; ?>

<?php  if ($videoFiles) {?>
<div class="video">
<h3>Смотреть</h3>
	<?php  foreach($videoFiles as $k=>$v) {?>
    <a href="<?=$v->tfields["file"]["url"]?>"><?=$v->name?></a>
    <?php  }?>
</div>
<?php  }?>


<?php  if ($audioFiles) {?>
<div class="video audio">
<h3>Слушать</h3>
	<?php  foreach($audioFiles as $k=>$v) {?>
    <a href="<?=$v->tfields["file"]["url"]?>"><?=$v->name?></a>
    <?php  }?>
</div>
<?php  }?>

<?php 
$photoes = $CurrentNode->getChildrenOfClass("photo");
if (count($photoes)) { ?>
<p>&nbsp;</p>
<table width="100%" celpadding="0" cellspacing="0" class="contentDetailTab">
<?php  for ($i=1; $i<=count($photoes); $i++) {
	$p =& $photoes[$i-1];
	$img = $p->tfields["photo"];
	if ($i%2 == 1) { ?>
	<tr>
	<?php  } ?>
		<td id="pad">
			<table cellpadding="0" cellspacing="0" >
				<tr>
					<?php  if ($img["thumburl"]) { ?>
					<td>
					 <table cellpadding="0" cellspacing="0">
					  <tr>
					   <td><img src="/_img/s.gif" width="5" height="<?= $img["thumbheight"]+28 ?>" alt="" class="shadowImgTL"></td>
					   <td><div class="shadowImgDetailTL_01">
							<div class="shadowImgDetailBR_01"><a href="<?= $p->getHref() ?>" onclick="enlarge(this, '', <?= $img['width']+30 ?>, <?= $img['height']+90 ?>); return false;"><img src="<?= $img["thumburl"] ?>" width="<?= $img["thumbwidth"] ?>" height="<?= $img["thumbheight"] ?>" alt=""></a></div>
						   </div></td>
					   <td><img src="/_img/s.gif" width="5" height="<?= $img["thumbheight"]+28 ?>" alt="" class="shadowImgBR"></td>
					   <?php  } ?>
					  </tr>
					  <tr>
					   <td colspan="3" align="right" style="vertical-align:top;"><img src="/_img/s.gif" width="103" height="5" alt="" class="shadowImgRNews"></td>
					  </tr>
					 </table>
					</td>
					
					<td class="text" rowspan="2"><?php  if ($p->tfields["name_"]) {?><?= $p->tfields["name_"] ?><?php  } ?></td>
				</tr>
			</table>
		</td>
	<?php  if ($i%2 == 0 || $i == count($photos)) { ?>
	</tr>
	<?php  } ?>	
<?php  } ?>
</table>
<?php  } ?>	
<?php  tinclude("_foot") ?>
