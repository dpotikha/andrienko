<?php
tinclude("_head");
$sections = $CurrentNode->getChildren();
?>
<section class="wrap page-head" style="padding-top:48px"><h1 class="h1"><?= e($CurrentNode->name) ?></h1></section>
<?php if (strlen(plainText($CurrentNode->tfields["text"] ?? ''))) { ?>
<div class="wrap rich"><?= richText($CurrentNode->tfields["text"] ?? '') ?></div>
<?php } ?>
<?php foreach ($sections as $s) {
	// the four most recently added projects of the section
	$latest = $s->getChildren("n.id DESC", 0, 4);
?>
<section class="wrap" style="padding-top:32px;padding-bottom:8px">
	<div class="rule" style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:baseline;gap:12px;padding-top:20px;margin-bottom:20px">
		<h2 class="h2" style="font-size:26px"><a href="<?= e($s->getHref()) ?>"><?= e($s->name) ?></a></h2>
		<a class="more" href="<?= e($s->getHref()) ?>">Усі роботи розділу →</a>
	</div>
	<div class="grid">
<?php foreach ($latest as $p) { ?>
		<a class="card" href="<?= e($p->getHref()) ?>"><?= frameImg($p->tfields["main_img"] ?? null) ?><span class="card-title"><?= e($p->name) ?></span></a>
<?php } ?>
	</div>
</section>
<?php } ?>
<div style="height:48px"></div>
<?php tinclude("_foot") ?>
