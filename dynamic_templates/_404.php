<?php
header("HTTP/1.1 404 Not Found");
tinclude("_head", array("pageTitle" => "Сторінку не знайдено"));
$news = Node::findByQuery("SELECT n.* FROM {news_item} n ORDER BY n.date DESC LIMIT 4");
?>
<section class="wrap" style="display:flex;flex-wrap:wrap;gap:48px;padding-top:64px;padding-bottom:72px">
	<div style="flex:1 1 420px;min-width:0">
		<p style="margin:0;font-family:Unbounded,sans-serif;font-size:clamp(80px,14vw,160px);line-height:1;font-weight:700;color:var(--accent)">404</p>
		<h1 class="h2" style="margin:16px 0 24px">Такої сторінки немає</h1>
<?php if (strlen(plainText($CurrentNode->tfields["text"] ?? ''))) { ?>
		<div class="rich"><?= richText($CurrentNode->tfields["text"] ?? '') ?></div>
<?php } ?>
		<nav class="chips" aria-label="Розділи" style="font-size:14px">
			<a class="btn btn-outline-accent" href="/">На головну</a>
			<a class="btn" href="/work">Діяльність</a>
			<a class="btn" href="/about/filmography">Фільмографія</a>
		</nav>
	</div>
	<div class="titles" style="flex:1 1 420px;min-width:0">
		<h2 class="h3" style="font-size:22px;margin-bottom:8px">Останні новини</h2>
<?php foreach ($news as $n) { ?>
		<a href="<?= e($n->getHref()) ?>"><time><?= e($n->tfields["date"]["formatted_date"]) ?></time><span><?= e($n->name) ?></span></a>
<?php } ?>
	</div>
</section>
<?php tinclude("_foot") ?>
