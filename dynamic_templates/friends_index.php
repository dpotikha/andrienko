<?
tinclude("_head");

if (strlen(trim($CurrentNode->tfields["text"]))) {
echo "<p>".$CurrentNode->tfields["text"]."</p>";
}

$friends = $CurrentNode->getChildren();
foreach($friends as $f) { 
$img 	 = $f->tfields["photo"];
$web_url = ((strlen($f->tfields["url"])) ? ("<h6 style=\"margin-top:3px\">Веб-сайт: <a href='".externalURL($f->tfields["url"])."'>".humanLikeUrl($f->tfields["url"])."</a></h6>") : "");
?>
<div class="newsMain" id="bgNo">
<table cellpadding="0" cellspacing="0" class="newsContent">
<tr>
<td width="5">
<? if ($img["url"]) { ?>

<table cellpadding="0" cellspacing="0" id="shadowTab">
<tr>
<td><img src="/_img/s.gif" width="5" height="<?= $img["thumbheight"]+18 ?>" alt="" class="shadowImgTL"></td>
<td><div class="shadowImgDetailTL_01">
<div class="shadowImgDetailBR_01" id="shadowNews">
<? if (strlen($f->tfields["article_name"]) && strlen($f->tfields["article_text"])) { ?>
<a href="<?= $f->getHref() ?>"><img src="<?= $img["thumburl"] ?>" width="<?= $img["thumbwidth"] ?>" height="<?= $img["thumbheight"] ?>" alt=""></a>
<? } else { ?>
<img src="<?= $img["thumburl"] ?>" width="<?= $img["thumbwidth"] ?>" height="<?= $img["thumbheight"] ?>" alt="">
<? } ?>
</div>
</div></td>
</tr>
<tr>
<td colspan="2" align="right"><img src="/_img/s.gif" width="90" height="5" alt=""></td>
</tr>
</table>
<? } ?>
</td>
<td class="textNews">
<b style="color:#424242"><?= $f->name ?></b>
<? if ($web_url) {?><?= $web_url ?><? } ?>
<? if ($f->tfields["announce"]) {?><div style="margin-top:8px"><?= $f->tfields["announce"] ?></div><? } ?>
<? if (strlen($f->tfields["article_name"]) && strlen($f->tfields["article_text"])) { ?>
<div class="newsLinkDetail" style="text-align:left"><a href="<?= $f->getHref() ?>"><?= $f->tfields["article_name"] ?></a><img src="/_img/arrow_02.gif" width="18" height="7" alt=""></div></td>
<? } ?>
</tr>
</table>
</div>
<?
}

tinclude("_foot");
?>