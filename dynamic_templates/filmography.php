<?php  tinclude("_head");

$filmsList = $CurrentNode->getChildren();

if (sizeof($filmsList)) { ?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td><img src="/_img/s.gif" width="6" height="5" alt=""></td>
<td>

<table cellpadding="0" cellspacing="0" border="0" class="filmography" width="100%">
<?php  for ($i=0; $i<sizeof($filmsList); $i++) { 
$fl =& $filmsList[$i];
//$img = $wl->tfields["main_img"];
$linkedNode = $fl->fields["link"]->getLinkedNode();?>
<tr><td width="9%"><?= $fl->tfields["year"]["year"]?></td><td width="49%" class="name"><?php  if ($linkedNode) {?><a href="<?= $linkedNode->getHref()?>"><?php  } ?><?= $fl->name ?><?php  if ($linkedNode) {?></a><?php  } ?></td><td width="21%"><?php if ($fl->tfields["position"]) {?><?= $fl->tfields["position"] ?><?php } else {?>&nbsp;<?php  } ?></td><td width="21%" class="role"><?php if ($fl->tfields["roles"]) {?><?= nl2br($fl->tfields["roles"]) ?><?php } else {?>&nbsp;<?php  } ?></td></tr>
<?php  } ?>
</table>
<?php  } else { ?>
<?= $CurrentNode->tfields["text"]?> 
<?php  } ?>

</td>
</tr>
</table>

<?php  tinclude("_foot") ?>