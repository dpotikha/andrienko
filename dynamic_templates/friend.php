<?php 
tinclude("_head");
$img = $CurrentNode->tfields["photo"];
$web_url = ((strlen($CurrentNode->tfields["url"])) ? ("<h6 style=\"margin-bottom:3px\"><a href='".externalURL($CurrentNode->tfields["url"])."'>".humanLikeUrl($CurrentNode->tfields["url"])."</a></h6>") : "");
if (strlen($web_url)) {	echo $web_url;	} 
?>
<div class="newsMain" id="bgNo">
	<?php 
	if (strlen(trim($CurrentNode->tfields["article_name"] ))) {
		echo "<h3>".$CurrentNode->tfields["article_name"]."</h3>";
	} 
	?>
	<table cellpadding="0" cellspacing="0" class="newsContent">
		<tr>
			<td id="textNews">
				<?php  if ($img["thumburl"]) { ?>
				<table cellpadding="0" cellspacing="0" id="shadowTab" style="float:left;">
					<tr>
						<td><img src="/_img/s.gif" width="5" height="<?= $img["thumbheight"]+20 ?>" alt="" class="shadowImgTL"></td>
						<td><div class="shadowImgDetailTL_01">
								<div class="shadowImgDetailBR_01" id="shadowNews">
								<?php  if ($img["url"]) { ?><a href="<?= $img["url"] ?>" onclick="enlarge(this, '<?= addslashes($CurrentNode->tfields["article_name"]) ?>',<?= $img["width"]+30 ?>,<?= $img["height"]+90 ?>); return false;"><?php  } ?>
								<img src="<?= $img["thumburl"] ?>" width="<?= $img["thumbwidth"] ?>" height="<?= $img["thumbheight"] ?>" alt="">
								<?php  if ($img["url"]) { ?></a><?php  } ?>
								</div>
							</div></td>
					</tr>
					<tr>
						<td colspan="2" align="right"><img src="/_img/s.gif" width="90" height="5" alt=""></td>
					</tr>
				</table>
				<?php  } ?>
				<?= $CurrentNode->tfields["article_text"] ?>
				</td>
		</tr>
	</table>
</div>
<?php 
$photoes = $CurrentNode->getChildren();
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
<?php  if ($i%2 == 0 || $i == count($photoes)) { ?>
</tr>
<?php  } ?>	
<?php  } ?>
</table>
<?php  } ?>	

<?php  define('OVERRIDE_NODE_PHOTO_ID', 'friends'); ?>

<?php  tinclude("_foot") ?>