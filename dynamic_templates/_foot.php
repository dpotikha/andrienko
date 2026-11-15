<?php  /* $parent = $CurrentNode->getParent();
echo $parent->shortname;*/ ?>
</td></tr>
<tr><td class="contentBg"><img src="/_img/content_corner_bottom.png" sm:iepng="true" width="498" height="70"></td></tr>
</table>
</td>
<td class="contentTdBg" style="height:100%;"><table cellpadding="0" cellspacing="0" width="100%" style="height:100%;" border="0"><tr><td class="contentTd01" style="height:100%;"><img src="/_img/top_content_td_01.jpg" width="49" height="60" alt=""></td></tr><tr><td style="height:auto;"><img src="/_img/content_corner_right.png" width="49" height="265" sm:iepng="true"></td></tr></table></td>
</tr>
</table>
<?php  
if ($CurrentNode->isRoot()) { 
$projects = Node::findByQuery("SELECT n.* FROM {project} n ORDER BY rand() LIMIT 2");
$project1= &$projects[0];
$project2= &$projects[1];
?>
<table width="100%" cellpadding="0" cellspacing="0" class="contentDetailTab" border="0" <?php if ($CurrentNode->isRoot()) {?> id="contentDetailTabIndex"<?php  } ?>>
<tr><td colspan="2"><img src="/_img/img_content_detail_hor.gif" width="50" height="40" alt=""><img src="/_img/h_content_detail_work.gif" width="200" height="40" alt=""></td></tr>
<tr>
<td id="pad">
<table cellpadding="0" cellspacing="0">
<tr>
<td>
<table cellpadding="0" cellspacing="0">
<tr>
<td><img src="/_img/s.gif" width="5" height="100" alt="" class="shadowImgTL"></td>
<td><div class="shadowImgDetailTL_01"><div class="shadowImgDetailBR_01"><a href="<?= $project1->getHref() ?>"><img src="<?php  if ($project1->tfields["main_img"]["url"]) { ?><?= $project1->tfields["main_img"]["url"] ?><?php  } else { ?>/_img/s.gif<?php  } ?>" width="91" height="123" alt=""></a></div></div></td>
<td style="vertical-align:bottom;"><img src="/_img/s.gif" width="5" height="151" alt="" class="shadowImgBR"></td>
</tr>
<tr><td colspan="3" align="right"><img src="/_img/s.gif" width="122" height="5" alt="" class="shadowImgR"></td></tr>
</table>
</td>
<td><a href="<?= $project1->getHref() ?>"><img src="<?= $project1->tfields["main_img_txt"]["url"] ?>" width="95" height="60" alt=""></a></td>
</tr>
</table> 
</td>
<td id="pad">
<table cellpadding="0" cellspacing="0" class="">
<tr>
<td>
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td><img src="/_img/s.gif" width="5" height="100" alt="" class="shadowImgTL"></td>
<td><div class="shadowImgDetailTL_01"><div class="shadowImgDetailBR_01">
<a href="<?= $project2->getHref() ?>"><img src="<?= $project2->tfields["main_img"]["url"] ?>" width="91" height="123" alt=""></a>
</div></div></td>
<td style="vertical-align:bottom;"><img src="/_img/s.gif" width="5" height="151" alt="" class="shadowImgBR"></td>
</tr>
<tr><td colspan="3" align="right"><img src="/_img/s.gif" width="122" height="5" alt="" class="shadowImgR"></td></tr>
</table>
 
</td>
<td><a href="<?= $project2->getHref() ?>"><img src="<?= $project2->tfields["main_img_txt"]["url"] ?>" width="95" height="60" alt=""></a></td>
</tr>
</table>
</td>
<td width="20">&nbsp;</td>
</tr>
</table>
<?php  } else {?>
<div style="padding:7px 0px 17px 45px;"><a href="/contacts"><img src="/_img/letter.gif" width="290" height="85" alt=""></a></div>
<?php  } ?>
</td>
<td width="100%" class="contentRightTd" <?php  if ($CurrentNode->isRoot()) {?> id="contentRightTdIndex"<?php  } ?>>
<?php  if (file_exists($_SERVER["DOCUMENT_ROOT"]."/_img/photo/photo_".$CurrentNode->shortname.".jpg")) {?>
<img src="/_img/photo/photo_<?= $CurrentNode->shortname ?>.jpg" width="353" height="392" alt="" style="display:block;background-color:#C1C1C1;">
<?php } elseif (defined("OVERRIDE_NODE_PHOTO_ID")) {?>
<img src="/_img/photo/photo_<?= OVERRIDE_NODE_PHOTO_ID?>.jpg" width="353" height="392" alt="" style="display:block;background-color:#C1C1C1;">
<?php  } else {?>
<img src="/_img/photo/photo_.jpg" width="353" height="392" alt="" style="display:block;background-color:#C1C1C1;">
<?php  }?>

<?php  $overridePhotoId = defined('OVERRIDE_NODE_PHOTO_ID') ? OVERRIDE_NODE_PHOTO_ID : ''; // only some templates define it; PHP 8 throws on an undefined constant ?>
<?php  if ($CurrentNode->shortname != 'news') { ?>

	<?php  if ($overridePhotoId != 'news') { ?>
		<div class="contntDetailNewsHead"><img src="/_img/h_content_detail_news.gif" width="120" height="50" alt=""></div>
	<?php  } else { ?>
		<img src="/_img/no-news-bg.gif" width="353" height="10"/>
	<?php  } ?>
	
	<table cellpadding="0" cellspacing="0" width="94%" border="0">
	<tr>
	<td class="contentDetailRight">
	
<?php  if ($CurrentNode->isRoot()) {
		$lastNews = Node::findByQuery("SELECT n.* FROM {news_item} n ORDER BY n.date DESC LIMIT 1");
	}
	else {
		$lastNews = Node::findByQuery("SELECT n.* FROM {news_item} n ORDER BY n.date DESC LIMIT 4");
	}
	for ($i=0; $i<sizeof($lastNews); $i++) {
		$n= &$lastNews[$i];
		$img = $n->tfields["photo"];
		if ($i == 0 && $overridePhotoId != 'news') { ?>
		
		<div class="newsMain"><a href="<?= $n->getHref() ?>" id="rev" class="newsName"><?= $n->name ?></a>
		<table cellpadding="0" cellspacing="0" class="newsContent" border="0">
		<tr>
		
		<td width="5">
		<?php  if ($img["url"]) { ?>
		<table cellpadding="0" cellspacing="0" id="shadowTab">
		<tr>
		<td><img src="/_img/s.gif" width="5" height="<?= $img["thumbheight"]+18 ?>" alt="" class="shadowImgTL"></td>
		<td><div class="shadowImgDetailTL_01"><div class="shadowImgDetailBR_01" id="shadowNews">
		<a href="<?= $n->getHref() ?>"><img src="<?= $img["thumburl"] ?>" width="<?= $img["thumbwidth"] ?>" height="<?= $img["thumbheight"] ?>" alt=""></a></div></div></td>
		</tr>
		<tr>
		<td colspan="2" align="right"><img src="/_img/s.gif" width="90" height="5" alt=""></td>
		</tr>
		</table> 
		<?php  } ?>   
		</td>
		
		<td class="textNews"><?= $n->tfields["announce"] ?>
		<?php  if ($CurrentNode->isRoot()) { ?>
			<div class="newsLinkDetail"><a href="/news">Читать другие новости</a><img src="/_img/arrow_01.gif" width="18" height="7" alt=""></div>
		<?php  } else { ?>
			<div class="newsLinkDetail"><a href="<?= $n->getHref() ?>">Читать подробнее</a><img src="/_img/arrow_01.gif" width="18" height="7" alt=""></div>
		<?php  } ?>
		</td>
		</tr>
		</table> 
		</div>
			
		<?php  }
		else {
			if ( ($i==1 && $overridePhotoId!='news') || ($overridePhotoId=='news' && $i==0) ) { ?><div class="otherNews"><?php  } ?>
			
			<div><h6 style="padding-left:0px"><?= siteFormattedDate($n->tfields["date"]["formatted_date"]) ?></h6><a href="<?= $n->getHref() ?>"><?= $n->name ?></a></div>
			
			<?php  if ($i==sizeof($lastNews)-1 && $i >= 1) { ?>
				</div>
				<div class="newsLinkDetail" id="newsLinkDetail"><a href="/news">Читать другие новости</a><img src="/_img/arrow_01.gif" width="18" height="7" alt=""></div> 
			<?php  } 
		} 									
	} 
?>
</td>
</tr>
</table> 
<br/>
<?php  } else { ?>
<img src="/_img/no-news-bg.gif" width="353" height="10"/>
<?php  } ?>
</td>
</tr> 
</table>
<table cellpadding="0" cellspacing="0" id="widthMain"  class="bottom">
<tr>
<td class="bottom_01"><img src="/_img/logo_bot.gif" width="205" height="60" alt=""><div>&copy; 2006. Все права принадлежат Виктору Андриенко</div></td>
<td class="bottom_02">Сайт сделали добрые люди в студии&nbsp;&nbsp;<a href="http://nate.com.ua/"><img src="/_img/logo_sakh.gif" width="71" height="33" alt=""></a></td></tr>
</table>
<!-- eiiao iniiaiie ?anoe -->
</td>
<td style="padding-top:227px;"><img src="/_img/s.gif" width="10" height="230" alt="" class="shadowBodyTR"></td>
</tr> 
<tr>
<td style="vertical-align:bottom;"><img src="/_img/s.gif" width="10" height="315" alt="" class="shadowBodyBL"></td>
<td style="vertical-align:bottom;"><img src="/_img/s.gif" width="10" height="410" alt="" class="shadowBodyBR"></td>
</tr> 
<tr>
<td colspan="3" align="right"><img src="/_img/s.gif" width="660" height="10" alt="" class="shadowBodyB"></td>
</tr>
</table>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-10706563-1");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>