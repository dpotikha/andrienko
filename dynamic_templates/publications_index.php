<?php 
tinclude("_head");
?>
<?php  if ($CurrentNode->tfields["main_img"]["url"]) {?>
<img src="<?= $CurrentNode->tfields["main_img"]["url"]; ?>" width="<?= $CurrentNode->tfields["main_img"]["width"]; ?>" height="<?= $CurrentNode->tfields["main_img"]["height"]; ?>" style="float:left; margin-right:15px" /><?php  } ?>
<?php  if ($CurrentNode->tfields["text"]) {?><?= $CurrentNode->tfields["text"]; ?><?php  } ?>
<div style="clear:left"></div>
<?php 
//$publications = Node::findByQuery("SELECT n.* FROM {pub} n ORDER BY n.date DESC");
//$publicationsCount = Node::findCountByQuery("SELECT COUNT(*) FROM {pub} n");

$publications = $CurrentNode->getChildrenOfClass("pub", "n.date DESC");
$publicationsCount = $CurrentNode->getChildrenCountOfClass("pub");

$rpp = 5;
$lpp = 10;
$pageNum = (int)$request->getParameter("page");
$offset = $pageNum * $rpp;

for($i=$offset; $i < ($offset + $rpp) && $i < $publicationsCount; $i++) { 
	$p =& $publications[$i];
	$img 	= $p->tfields["image"];
	$author = ' | '. $p->tfields["author"];
	$source = ' | '. (($p->tfields["source_url"]) ? ('<a href="'.externalURL($p->tfields["source_url"]).'">'.$p->tfields["source"].'</a>') : $p->tfields["source"]);
?>
<div class="newsMain" id="bgNo">
<h6 style="padding-left:0px"><?= $p->tfields["date"]["formatted_date"] ?>
<?php 
if (strlen($author)) { echo $author; } 
if (strlen($source)) { echo $source; } 
?></h6>
<a href="<?= $p->getHref() ?>" class="newsName"><?= $p->name ?></a>
<table cellpadding="0" cellspacing="0" border="0" class="newsContent">
<tr>
<?php  if ($img["url"]) { ?>
<td width="5">
<table cellpadding="0" cellspacing="0" border="0" id="shadowTab">
<tr>
<td><img src="/_img/s.gif" width="5" height="<?= $img["thumbheight"]+18 ?>" alt="" class="shadowImgTL"></td>
<td><div class="shadowImgDetailTL_01">
<div class="shadowImgDetailBR_01" id="shadowNews"><a href="<?= $p->getHref() ?>"><img src="<?= $img["thumburl"] ?>" width="<?= $img["thumbwidth"] ?>" height="<?= $img["thumbheight"] ?>" alt=""></a></div></div></td>
</tr>
<tr>
<td colspan="2" align="right"><img src="/_img/s.gif" width="90" height="5" alt=""></td>
</tr>
</table>
</td><?php  } ?>
<td  class="textNews"><?= $p->tfields["announce"] ?></td>
</tr>
</table>
<div class="newsLinkDetail"><a href="<?= $p->getHref() ?>">Читать подробнее</a><img src="/_img/arrow_02.gif" width="18" height="7" alt=""></div>
</div>
<?php  } ?>
<?php  if ($publicationsCount > $rpp) { ?>
<div class="pager"><table class="pager">
<?php  HTMLWriter::draw_paging_links($offset, $rpp, $lpp, $publicationsCount, array(), array(), "_draw_paging_link");?> 
</table></div>
<?php  } ?>
<?php  tinclude("_foot"); ?>