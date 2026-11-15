<?php
// one filmography entry (class movie); this template did not exist before the redesign
tinclude("_head");
$f = $CurrentNode;
$y = (int)$f->tfields["year"]["year"];
$linked = isset($f->fields["link"]) ? $f->fields["link"]->getLinkedNode() : null;
$linkedClass = $linked ? $linked->getNodeClass()->shortname : '';
$poster = ($linked && $linkedClass == "project") ? ($linked->tfields["main_img"] ?? null) : null;
$shots = array();
if ($linked && $linkedClass == "project") {
	foreach ($linked->getChildrenOfClass("photo") as $p) {
		if (hasImg($p->tfields["photo"]) && count($shots) < 3) $shots[] = $p;
	}
}
$facts = array(
	"Робота" => trim($f->tfields["position"] ?? ''),
	"Роль" => trim(plainText($f->tfields["roles"] ?? '')),
	"Студія / канал" => trim($f->tfields["studio"] ?? ''),
	"Формат" => trim($f->tfields["series"] ?? ''),
);
// neighbours in the filmography, newest first
$all = $f->getParent()->getChildren();
usort($all, function ($a, $b) { return (int)$b->tfields["year"]["year"] <=> (int)$a->tfields["year"]["year"]; });
$prev = $next = null;
foreach ($all as $k => $n) {
	if ($n->id == $f->id) { $prev = $all[$k - 1] ?? null; $next = $all[$k + 1] ?? null; break; }
}
?>
<?= crumbs($f) ?>
<section class="wrap split" style="padding-top:28px;padding-bottom:16px">
<?php if (hasImg($poster)) { ?>
	<img class="fit" src="<?= e($poster["url"]) ?>" alt="" style="flex:0 1 <?= min(360, (int)$poster["width"]) ?>px;min-width:0">
<?php } ?>
	<div class="main">
		<p class="kicker"><?= $y ?: '' ?></p>
		<h1 class="h1" style="margin-bottom:28px"><?= e($f->name) ?></h1>
		<dl class="facts">
<?php foreach ($facts as $k => $v) { if ($v === '') continue; ?>
			<dt><?= $k ?></dt><dd><?= e($v) ?></dd>
<?php } ?>
		</dl>
<?php if ($linked) { ?>
		<p style="margin:28px 0 0"><a class="btn btn-outline-accent" href="<?= e($linked->getHref()) ?>"><?= $linkedClass == "project" ? "Проєкт" : "Детальніше" ?>: <?= e($linked->name) ?> →</a></p>
<?php } ?>
	</div>
</section>
<?php if ($shots) { ?>
<section class="wrap grid" style="padding-top:24px" aria-label="Кадри">
<?php foreach ($shots as $p) { ?>
	<?= photoLink($p, "film") ?>
<?php } ?>
</section>
<?php } ?>
<nav class="wrap" aria-label="Сусідні роботи" style="padding-bottom:48px">
	<div class="pager-links">
		<?php if ($prev) { ?><a href="<?= e($prev->getHref()) ?>">← <?= (int)$prev->tfields["year"]["year"] ?> · <?= e($prev->name) ?></a><?php } else { ?><span></span><?php } ?>
		<?php if ($next) { ?><a href="<?= e($next->getHref()) ?>"><?= (int)$next->tfields["year"]["year"] ?> · <?= e($next->name) ?> →</a><?php } ?>
	</div>
</nav>
<?php tinclude("_foot") ?>
