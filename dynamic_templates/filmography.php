<?php
tinclude("_head");
$films = $CurrentNode->getChildren();
usort($films, function ($a, $b) {
	return (int)$b->tfields["year"]["year"] <=> (int)$a->tfields["year"]["year"];
});
$decades = array();
foreach ($films as $f) {
	$y = (int)$f->tfields["year"]["year"];
	if ($y) $decades[intdiv($y, 10) * 10] = true;
}
krsort($decades);
?>
<?= crumbs($CurrentNode) ?>
<section class="wrap page-head">
	<p class="kicker">Кіно · серіали · телепроєкти · дубляж</p>
	<h1 class="h1" style="margin-bottom:28px"><?= e($CurrentNode->name) ?></h1>
<?php if ($films) { ?>
	<div class="chips" role="group" aria-label="Десятиліття">
		<button type="button" class="chip" data-decade="all" aria-pressed="true">УСІ</button>
<?php foreach (array_keys($decades) as $dc) { ?>
		<button type="button" class="chip" data-decade="<?= $dc ?>" aria-pressed="false"><?= $dc ?>-ті</button>
<?php } ?>
	</div>
<?php } ?>
</section>
<section class="wrap" style="padding-top:8px;padding-bottom:64px">
<?php if ($films) { ?>
	<div class="scroll-x">
		<table class="film-table">
			<thead><tr><th scope="col" style="width:80px">РІК</th><th scope="col">НАЗВА</th><th scope="col">РОБОТА · РОЛЬ</th><th scope="col">СТУДІЯ / КАНАЛ</th><th scope="col">ФОРМАТ</th></tr></thead>
			<tbody>
<?php foreach ($films as $f) {
	$y = (int)$f->tfields["year"]["year"];
	$work = array_filter(array(trim($f->tfields["position"] ?? ''), trim(plainText($f->tfields["roles"] ?? ''))), 'strlen');
	// as before: a title links only when the entry points to a page with content (project or news)
	$linked = isset($f->fields["link"]) ? $f->fields["link"]->getLinkedNode() : null;
?>
				<tr data-decade="<?= $y ? intdiv($y, 10) * 10 : '' ?>">
					<td><?= $y ?: '' ?></td>
					<td><?= $linked ? '<a href="' . e($linked->getHref()) . '">' . e($f->name) . '</a>' : e($f->name) ?></td>
					<td><?= e(implode(' · ', $work)) ?></td>
					<td><?= e($f->tfields["studio"] ?? '') ?></td>
					<td><?= e($f->tfields["series"] ?? '') ?></td>
				</tr>
<?php } ?>
			</tbody>
		</table>
	</div>
<?php } else { ?>
	<div class="rich"><?= richText($CurrentNode->tfields["text"] ?? '') ?></div>
<?php } ?>
</section>
<?php tinclude("_foot") ?>
