<?php
tinclude("_head");
$img = $CurrentNode->tfields["photo"] ?? null;
$url = trim($CurrentNode->tfields["url"] ?? '');
$ann = plainText($CurrentNode->tfields["announce"] ?? '');
$photos = array();
foreach ($CurrentNode->getChildren() as $c) {
	if (hasImg($c->tfields["photo"] ?? null)) $photos[] = $c;
}
?>
<?= crumbs($CurrentNode) ?>
<section class="wrap split" style="padding-top:28px;padding-bottom:16px">
<?php if (hasImg($img)) { ?>
	<a class="js-view" data-group="friend" data-caption="<?= e($CurrentNode->name) ?>" href="<?= e($img["url"]) ?>" style="flex:0 1 <?= min(380, (int)$img["width"]) ?>px;min-width:0"><img class="fit" src="<?= e($img["url"]) ?>" alt="<?= e($CurrentNode->name) ?>"></a>
<?php } ?>
	<div class="main">
		<h1 class="h1" style="margin-bottom:12px"><?= e($CurrentNode->name) ?></h1>
<?php if ($ann !== '') { ?>
		<p class="body" style="margin:0 0 28px;font-size:19px;line-height:1.55"><?= e($ann) ?></p>
<?php } ?>
<?php if (strlen(trim($CurrentNode->tfields["article_name"] ?? ''))) { ?>
		<h2 class="h3" style="font-size:21px;line-height:1.35;margin-bottom:16px"><?= e(plainText($CurrentNode->tfields["article_name"])) ?></h2>
<?php } ?>
		<div class="rich"><?= $CurrentNode->tfields["article_text"] ?? '' ?></div>
<?php if ($url !== '' && strpos($url, '@') === false) { ?>
		<a class="more" href="<?= e(externalURL($url)) ?>"><?= e(humanLikeUrl(externalURL($url))) ?> ↗</a>
<?php } ?>
	</div>
</section>
<?php if ($photos) { ?>
<section class="wrap" style="padding-top:32px;padding-bottom:48px">
	<div class="grid" style="grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:24px">
<?php foreach ($photos as $p) { ?>
		<figure style="margin:0"><?= photoLink($p, "friend") ?><figcaption class="muted" style="margin-top:8px;font-size:14px;line-height:1.45"><?= e(plainText($p->tfields["name_"] ?? '')) ?></figcaption></figure>
<?php } ?>
	</div>
</section>
<?php } ?>
<?php tinclude("_foot") ?>
