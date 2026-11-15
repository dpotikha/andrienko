<?php
tinclude("_head");
$sub = $CurrentNode->getChildren();
$photo = $CurrentNode->tfields["main_img"] ?? null;
?>
<section class="wrap split" style="padding-top:48px;padding-bottom:64px">
	<div class="main">
		<h1 class="h1" style="margin-bottom:28px"><?= e($CurrentNode->name) ?></h1>
		<div class="rich"><?= richText($CurrentNode->tfields["text"] ?? '') ?></div>
<?php if ($sub) { ?>
		<nav class="chips" aria-label="Розділи" style="margin-top:28px;font-size:14px">
<?php foreach ($sub as $k => $s) { ?>
			<a class="btn<?= $k == 0 ? ' btn-outline-accent' : '' ?>" href="<?= e($s->getHref()) ?>"><?= e($s->name) ?> →</a>
<?php } ?>
		</nav>
<?php } ?>
	</div>
<?php if ($CurrentNode->absolutePath == "/about") { ?>
	<img class="side fit" src="/assets/about.jpg" width="1073" height="1504" alt="Віктор Андрієнко" style="width:400px">
<?php } ?>
</section>
<?php tinclude("_foot") ?>
