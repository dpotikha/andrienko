<?php
tinclude("_head");
// same list layout as the news; publications and stories are few, so one page
$items = $CurrentNode->getChildrenOfClass("pub", "n.date DESC");
?>
<?= crumbs($CurrentNode) ?>
<section class="wrap page-head"><h1 class="h1"><?= e($CurrentNode->name) ?></h1></section>
<?php if (strlen(plainText($CurrentNode->tfields["text"] ?? ''))) { ?>
<div class="wrap" style="display:flex;flex-wrap:wrap;gap:32px;align-items:flex-start;padding-bottom:16px">
<?php if (hasImg($CurrentNode->tfields["main_img"] ?? null)) { ?>
	<img class="fit" src="<?= e($CurrentNode->tfields["main_img"]["url"]) ?>" alt="" style="flex:0 1 auto">
<?php } ?>
	<div class="rich" style="flex:1 1 480px;min-width:0;max-width:720px"><?= $CurrentNode->tfields["text"] ?></div>
</div>
<?php } ?>
<section class="wrap" style="padding-top:8px;padding-bottom:64px">
<?php foreach ($items as $p) {
	$meta = array(e($p->tfields["date"]["formatted_date"]));
	if (strlen(trim($p->tfields["author"] ?? ''))) $meta[] = e($p->tfields["author"]);
	if (strlen(trim($p->tfields["source"] ?? ''))) $meta[] = e($p->tfields["source"]);
?>
	<?= listRow($p, "image", implode(' · ', $meta)) ?>
<?php } ?>
</section>
<?php tinclude("_foot") ?>
