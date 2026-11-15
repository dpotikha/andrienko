<?php
tinclude("_head");
$photos = array();
foreach ($CurrentNode->getChildrenOfClass("photo") as $p) {
	if (hasImg($p->tfields["photo"])) $photos[] = $p;
}
$media = array_merge($CurrentNode->getChildrenOfClass("video"), $CurrentNode->getChildrenOfClass("audio"));
$section = $CurrentNode->getParent();
$siblings = array();
foreach ($section->getChildren() as $s) {
	if ($s->id != $CurrentNode->id) $siblings[] = $s;
}
?>
<?= crumbs($CurrentNode) ?>
<section class="wrap page-head">
	<p class="kicker"><?= e($section->name) ?></p>
	<h1 class="h1" style="max-width:900px"><?= e($CurrentNode->name) ?></h1>
</section>
<?php if ($photos) { $first = $photos[0]; $fi = $first->tfields["photo"]; ?>
<section class="wrap" aria-label="Галерея" style="padding-bottom:24px">
	<figure class="stage" style="margin:0">
		<div class="stage-img" title="Відкрити на весь екран"><img src="<?= e($fi["url"]) ?>" alt="<?= e(plainText($first->tfields["name_"] ?? '')) ?>"></div>
		<figcaption class="stage-bar">
			<span class="stage-cap"><?= e(plainText($first->tfields["name_"] ?? '')) ?></span>
			<span style="display:flex;align-items:center;gap:8px">
				<span class="stage-n muted">1 / <?= count($photos) ?></span>
				<button type="button" data-s="prev" aria-label="Попередній кадр">←</button>
				<button type="button" data-s="next" aria-label="Наступний кадр">→</button>
			</span>
		</figcaption>
	</figure>
	<div class="thumbs">
<?php foreach ($photos as $k => $p) { $pi = $p->tfields["photo"]; $cap = plainText($p->tfields["name_"] ?? ''); ?>
		<a class="js-view" id="p<?= (int)$p->id ?>" data-group="project" data-caption="<?= e($cap) ?>" href="<?= e($pi["url"]) ?>"<?= $k == 0 ? ' aria-current="true"' : '' ?>><span class="frame"><img src="<?= e(!empty($pi["thumburl"]) ? $pi["thumburl"] : $pi["url"]) ?>" alt="<?= e($cap) ?>" loading="lazy"></span></a>
<?php } ?>
	</div>
</section>
<?php } ?>
<section class="wrap project-body" style="padding-top:24px;padding-bottom:64px">
	<div class="rich"><?= richText($CurrentNode->tfields["text"] ?? '') ?></div>
	<aside>
<?php if ($media) { ?>
		<div class="mono muted" style="font-size:12px;letter-spacing:.08em;margin-bottom:8px">ВІДЕО Й АУДІО</div>
		<ul class="media-list">
<?php foreach ($media as $m) {
	$url = $m->tfields["file"]["url"] ?? '';
	$ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));
?>
			<li><?= e($m->name) ?>
<?php if (in_array($ext, array("mp3", "m4a", "ogg", "wav"))) { ?>
				<audio controls preload="none" src="<?= e($url) ?>"></audio>
<?php } elseif (in_array($ext, array("mp4", "webm", "m4v"))) { ?>
				<video controls preload="none" src="<?= e($url) ?>"></video>
<?php } elseif ($url) { ?>
				<br><a class="more" href="<?= e($url) ?>">Завантажити файл (<?= e($ext) ?>)</a>
<?php } ?>
			</li>
<?php } ?>
		</ul>
<?php } ?>
<?php if ($siblings) { ?>
		<div class="rule side-list" style="padding-top:16px;margin-top:<?= $media ? 24 : 8 ?>px">
			<div class="mono muted" style="font-size:12px;letter-spacing:.08em;margin-bottom:8px">ІНШЕ: <?= e(mb_strtoupper($section->name)) ?></div>
<?php foreach ($siblings as $s) { ?>
			<a href="<?= e($s->getHref()) ?>"><?= e($s->name) ?></a>
<?php } ?>
		</div>
<?php } ?>
	</aside>
</section>
<?php tinclude("_foot") ?>
