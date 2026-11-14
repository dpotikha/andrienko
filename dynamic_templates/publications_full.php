<?php 
tinclude("_head");
$img = $CurrentNode->tfields["image"];
$author = " | <b>". $CurrentNode->tfields["author"] ."</b>";
$source = " | ". (($CurrentNode->tfields["source_url"]) ? ("<a href='".externalURL($CurrentNode->tfields["source_url"])."'>".$CurrentNode->tfields["source"]."</a>") : $CurrentNode->tfields["source"]);
?>

<div class="newsMain" id="bgNo">
<h6><?= $CurrentNode->tfields["date"]["formatted_date"] ?>
<?php 
if (strlen($author)) { echo $author; } 
if (strlen($source)) { echo $source; } 
?> 
</h6>
<h3><?= $CurrentNode->name ?></h3>
<table cellpadding="0" cellspacing="0" border="0" class="newsContent">
<tr>
<td id="textNews">
<?php  if ($img["thumburl"]) { ?><table cellpadding="0" cellspacing="0" border="0" id="shadowTab" style="float:left;">
<tr>
<td><img src="/_img/s.gif" width="5" height="<?= $img["thumbheight"]+20 ?>" alt="" class="shadowImgTL"></td>
<td><div class="shadowImgDetailTL_01">
<div class="shadowImgDetailBR_01" id="shadowNews">
<?php  if ($img["url"]) { ?><a href="<?= $img["url"] ?>" onclick="enlarge(this,'<?= addslashes($CurrentNode->name) ?>',<?= $img["width"]+30 ?>,<?= $img["height"]+90 ?>); return false;"><?php  } ?>
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
<?= $CurrentNode->tfields["text"] ?>
</td>
</tr>
</table>
</div>
<div class="newsLinkDetail"><a href="./">Вернуться к списку публикаций</a></div>
<?php  
define('OVERRIDE_NODE_PHOTO_ID', 'publications');
tinclude("_foot") ?>