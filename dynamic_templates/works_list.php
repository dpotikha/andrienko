<?php
tinclude("_head");
$projects = $CurrentNode->getChildren();
$work = $CurrentNode->getParent();
$sections = $work ? $work->getChildren() : array();
?>
<?= crumbs($CurrentNode) ?>
<section class="wrap page-head">
	<h1 class="h1" style="margin-bottom:20px"><?= e($CurrentNode->name) ?></h1>
	<nav class="chips" aria-label="Розділи діяльності">
<?php foreach ($sections as $s) { ?>
		<a class="chip" href="<?= e($s->getHref()) ?>"<?= $s->id == $CurrentNode->id ? ' aria-current="page"' : '' ?>><?= e($s->name) ?></a>
<?php } ?>
	</nav>
</section>
<section class="wrap" style="padding-top:16px;padding-bottom:64px">
<?php if ($projects) { ?>
	<div class="grid">
<?php foreach ($projects as $p) { ?>
		<a class="card" href="<?= e($p->getHref()) ?>"><?= frameImg($p->tfields["main_img"] ?? null) ?><span class="card-title"><?= e($p->name) ?></span></a>
<?php } ?>
	</div>
<?php } else { ?>
	<div class="rich"><?= richText($CurrentNode->tfields["text"] ?? '') ?></div>
<?php } ?>
</section>
<?php tinclude("_foot") ?>
