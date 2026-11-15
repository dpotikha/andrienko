<?php
// one article: news item, story or publication. Params: $imgField, $meta (HTML), $backHref, $backLabel, $older, $newer
$img = $CurrentNode->tfields[$imgField] ?? null;
$photos = array();
foreach ($CurrentNode->getChildren() as $c) {
	if (hasImg($c->tfields["photo"] ?? null)) $photos[] = $c;
}
?>
<?= crumbs($CurrentNode) ?>
<article class="wrap article" style="padding-top:28px;padding-bottom:16px">
	<div class="article-main">
		<div class="row-meta" style="color:var(--accent)"><?= $meta ?></div>
		<h1 class="h1" style="font-size:clamp(28px,3.6vw,44px);line-height:1.12;margin:10px 0 28px"><?= e($CurrentNode->name) ?></h1>
		<div class="rich"><?= richText($CurrentNode->tfields["text"] ?? '') ?></div>
	</div>
<?php if (hasImg($img) || $photos) { ?>
	<aside class="article-side" aria-label="Фото">
<?php if (hasImg($img)) { ?>
		<a class="js-view" data-group="article" data-caption="<?= e($CurrentNode->name) ?>" href="<?= e($img["url"]) ?>"><img src="<?= e($img["url"]) ?>" alt="" style="max-width:100%;height:auto"></a>
<?php } ?>
<?php foreach ($photos as $p) { $pi = $p->tfields["photo"]; $cap = plainText($p->tfields["name_"] ?? ''); ?>
		<a class="js-view" id="p<?= (int)$p->id ?>" data-group="article" data-caption="<?= e($cap) ?>" href="<?= e($pi["url"]) ?>"><img src="<?= e($pi["url"]) ?>" alt="<?= e($cap) ?>" style="max-width:100%;height:auto" loading="lazy"></a>
<?php } ?>
	</aside>
<?php } ?>
</article>
<nav class="wrap" aria-label="Інші матеріали" style="padding-bottom:48px">
	<div class="pager-links">
		<?php if (!empty($older)) { ?><a href="<?= e($older->getHref()) ?>">← <?= e($older->tfields["date"]["formatted_date"]) ?> · <?= e($older->name) ?></a><?php } else { ?><span></span><?php } ?>
		<?php if (!empty($newer)) { ?><a href="<?= e($newer->getHref()) ?>"><?= e($newer->tfields["date"]["formatted_date"]) ?> · <?= e($newer->name) ?> →</a><?php } ?>
		<a href="<?= e($backHref) ?>" style="color:var(--accent)"><?= e($backLabel) ?></a>
	</div>
</nav>
