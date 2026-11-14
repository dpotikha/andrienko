<? tinclude("_head");

$filmsList = $CurrentNode->getChildren();

if (sizeof($filmsList)) { ?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td><img src="/_img/s.gif" width="6" height="5" alt=""></td>
<td>

<table cellpadding="0" cellspacing="0" border="0" class="filmography" width="100%">
<? for ($i=0; $i<sizeof($filmsList); $i++) { 
$fl =& $filmsList[$i];
//$img = $wl->tfields["main_img"];
$linkedNode = $fl->fields["link"]->getLinkedNode();?>
<tr><td width="9%"><?= $fl->tfields["year"]["year"]?></td><td width="49%" class="name"><? if ($linkedNode) {?><a href="<?= $linkedNode->getHref()?>"><? } ?><?= $fl->name ?><? if ($linkedNode) {?></a><? } ?></td><td width="21%"><?if ($fl->tfields["position"]) {?><?= $fl->tfields["position"] ?><?} else {?>&nbsp;<? } ?></td><td width="21%" class="role"><?if ($fl->tfields["roles"]) {?><?= $fl->tfields["roles"] ?><?} else {?>&nbsp;<? } ?></td></tr>
<? } ?>
</table>
<? } else { ?>
<?= $CurrentNode->tfields["text"]?> 
<? } ?>

</td>
</tr>
</table>

<? tinclude("_foot") ?>