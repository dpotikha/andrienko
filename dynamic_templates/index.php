<?php
tinclude("_head");
// home photo: the Index node's own image field when it exists, else the portrait shipped with the design
$heroImg = $CurrentNode->tfields["hero_img"] ?? null;
if (hasImg($heroImg)) {
	$heroSrc = $heroImg["url"]; $heroW = $heroImg["width"]; $heroH = $heroImg["height"];
} else {
	$heroSrc = "/assets/hero.jpg"; $heroW = 655; $heroH = 984;
}
$work = Node::findByPath("/work");
$sections = $work ? $work->getChildren() : array();
// four projects with a picture, in random order (as the old home page did with two)
$projects = array();
foreach (Node::findByQuery("SELECT n.* FROM {project} n ORDER BY rand() LIMIT 16") as $p) {
	if (hasImg($p->tfields["main_img"]) && count($projects) < 4) $projects[] = $p;
}
$news = Node::findByQuery("SELECT n.* FROM {news_item} n ORDER BY n.date DESC LIMIT 7");
$lead = array_shift($news);
?>
<section class="wrap hero">
	<div class="hero-text">
		<p class="kicker" style="font-size:14px;letter-spacing:.1em">Актор · гуморист · сценарист · ведучий</p>
		<h1>Віктор<br>Андрієнко</h1>
		<div class="rich" style="max-width:560px"><?= $CurrentNode->tfields["text"] ?></div>
		<p style="display:flex;flex-wrap:wrap;gap:12px;margin:28px 0 0">
			<a class="btn btn-accent" href="/work">Дивитися роботи</a>
			<a class="btn" href="/about/filmography">Фільмографія</a>
		</p>
	</div>
	<figure class="hero-photo"><img class="fit" src="<?= e($heroSrc) ?>" width="<?= (int)$heroW ?>" height="<?= (int)$heroH ?>" alt="Віктор Андрієнко"></figure>
</section>

<section class="wrap section">
	<div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:baseline;gap:12px;margin-bottom:24px">
		<h2 class="h2">Діяльність</h2>
		<nav class="chips" aria-label="Розділи діяльності">
<?php foreach ($sections as $s) { ?>
			<a class="chip" href="<?= e($s->getHref()) ?>"><?= e(mb_strtoupper($s->name)) ?></a>
<?php } ?>
		</nav>
	</div>
	<div class="grid">
<?php foreach ($projects as $p) { ?>
		<a class="card" href="<?= e($p->getHref()) ?>" style="background:var(--surface)">
			<?= frameImg($p->tfields["main_img"]) ?>
			<span style="display:block;padding:14px 16px 18px">
				<span class="mono" style="display:block;font-size:12px;color:var(--accent)"><?= e(mb_strtoupper(sectionName($p))) ?></span>
				<span style="display:block;font-family:Unbounded,sans-serif;font-size:17px;margin-top:6px"><?= e($p->name) ?></span>
			</span>
		</a>
<?php } ?>
	</div>
</section>

<?php if ($lead) { ?>
<section class="rule">
	<div class="wrap section news-teaser">
		<div>
			<h2 class="h2" style="margin-bottom:20px">Новини</h2>
<?php if (hasImg($lead->tfields["photo"])) { ?>
			<a href="<?= e($lead->getHref()) ?>" tabindex="-1" aria-hidden="true"><img class="fit" src="<?= e($lead->tfields["photo"]["url"]) ?>" alt="" style="max-width:min(100%,<?= (int)$lead->tfields["photo"]["width"] ?>px)"></a>
<?php } ?>
			<time class="mono muted" style="display:block;font-size:13px;margin-top:14px"><?= e($lead->tfields["date"]["formatted_date"]) ?></time>
			<h3 class="h3" style="font-size:22px;margin-top:6px"><a href="<?= e($lead->getHref()) ?>"><?= e($lead->name) ?></a></h3>
		</div>
		<div class="titles">
<?php foreach ($news as $n) { ?>
			<a href="<?= e($n->getHref()) ?>"><time><?= e($n->tfields["date"]["formatted_date"]) ?></time><span><?= e($n->name) ?></span></a>
<?php } ?>
			<a href="/news" style="color:var(--accent)"><span>Увесь архів новин →</span></a>
		</div>
	</div>
</section>
<?php } ?>
<?php tinclude("_foot") ?>
