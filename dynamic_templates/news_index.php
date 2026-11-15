<?php
tinclude("_head");
// one page per year; the year chips list every year that has news
$years = array();
foreach (Node::findByQuery("SELECT n.* FROM {news_item} n ORDER BY n.date DESC") as $n) {
	$y = (int)$n->tfields["date"]["year"];
	if ($y) $years[$y][] = $n;
}
$year = (int)$request->getParameter("year");
if (!isset($years[$year])) $year = (int)key($years);
?>
<section class="wrap page-head" style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:flex-end;gap:20px;padding-top:48px">
	<h1 class="h1"><?= e($CurrentNode->name) ?></h1>
	<nav class="chips" aria-label="Роки">
<?php foreach (array_keys($years) as $y) { ?>
		<a class="chip" href="<?= e($CurrentNode->getHref()) ?>?year=<?= $y ?>"<?= $y == $year ? ' aria-current="page"' : '' ?>><?= $y ?></a>
<?php } ?>
	</nav>
</section>
<?php if (strlen(trim(plainText($CurrentNode->tfields["text"] ?? '')))) { ?>
<div class="wrap rich"><?= $CurrentNode->tfields["text"] ?></div>
<?php } ?>
<section class="wrap" style="padding-top:8px;padding-bottom:64px">
<?php foreach ($years[$year] ?? array() as $n) { ?>
	<?= listRow($n, "photo", e($n->tfields["date"]["formatted_date"])) ?>
<?php } ?>
</section>
<?php tinclude("_foot") ?>
