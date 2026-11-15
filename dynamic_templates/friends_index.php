<?php
tinclude("_head");
$friends = $CurrentNode->getChildren();
?>
<section class="wrap page-head" style="padding-top:48px">
	<p class="kicker">Колеги · автори · партнери</p>
	<h1 class="h1"><?= e($CurrentNode->name) ?></h1>
</section>
<?php if (strlen(plainText($CurrentNode->tfields["text"] ?? ''))) { ?>
<div class="wrap rich" style="max-width:780px"><?= richText($CurrentNode->tfields["text"] ?? '') ?></div>
<?php } ?>
<section class="wrap" style="padding-top:24px;padding-bottom:64px">
	<div class="grid" style="grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:40px 24px">
<?php foreach ($friends as $f) {
	// a friend gets a page of their own only when there is an article about them (as before)
	$hasPage = strlen(trim($f->tfields["article_name"] ?? '')) && strlen(trim($f->tfields["article_text"] ?? ''));
	$url = trim($f->tfields["url"] ?? '');
	$ann = plainText($f->tfields["announce"] ?? '');
?>
		<article>
			<?= $hasPage ? '<a href="' . e($f->getHref()) . '" tabindex="-1" aria-hidden="true">' : '' ?><?= frameImg($f->tfields["photo"] ?? null, $f->name, 'frame frame-34') ?><?= $hasPage ? '</a>' : '' ?>
			<h2 class="h3" style="font-size:18px;margin-top:14px"><?= $hasPage ? '<a href="' . e($f->getHref()) . '">' . e($f->name) . '</a>' : e($f->name) ?></h2>
<?php if ($ann !== '') { ?>
			<p class="body" style="margin:6px 0 0;font-size:15px;line-height:1.25"><?= e($ann) ?></p>
<?php } ?>
<?php if ($url !== '' && strpos($url, '@') === false) { ?>
			<a class="mono" style="display:inline-block;margin-top:8px;font-size:13px;color:var(--accent)" href="<?= e(externalURL($url)) ?>"><?= e(humanLikeUrl(externalURL($url))) ?></a>
<?php } ?>
		</article>
<?php } ?>
	</div>
</section>
<?php tinclude("_foot") ?>
