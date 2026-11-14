<?php  tinclude("_head");

$worksLinks = $CurrentNode->getChildren();

if (sizeof($worksLinks)) { ?>

<table width="100%" cellpadding="0" cellspacing="0" class="contentDetailTab">
	<?php  for ($i=0; $i<sizeof($worksLinks); $i++) { 
	$wl =& $worksLinks[$i];
	$img = $wl->tfields["main_img"];
?>
	<tr>
		<td id="pad">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="padding-bottom:15px;"><table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td><img src="/_img/s.gif" width="5" height="<?= $img["thumbheight"]+28 ?>" alt="" class="shadowImgTL"></td>
								<td><div class="shadowImgDetailTL_01">
										<div class="shadowImgDetailBR_01"> <a href="<?= $wl->getHref() ?>"><img src="<?= $img["thumburl"] ?>" width="<?= $img["thumbwidth"] ?>" height="<?= $img["thumbheight"] ?>" alt=""></a></div>
									</div></td>
								<td style="vertical-align:bottom;"><img src="/_img/s.gif" width="5" height="<?= $img["thumbheight"]+28 ?>" alt="" class="shadowImgBR"></td>
							</tr>
							<tr>
								<td colspan="3" align="right"><img src="/_img/s.gif" width="<?= $img["thumbwidth"]+31 ?>" height="5" alt="" class="shadowImgR"></td>
							</tr>
						</table></td>
					<td><h4><a href="<?= $wl->getHref() ?>">
							<?= $wl->name ?>
							</a></h4></td>
				</tr>
			</table>
		</td>
	</tr>
	<?php  } ?>
</table>
<?php  } else { ?>
<?= $CurrentNode->tfields["text"]?> 
<?php  } ?>
<?php  tinclude("_foot") ?>