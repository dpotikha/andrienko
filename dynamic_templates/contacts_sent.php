<?php tinclude("_head") ?>
<section class="wrap" style="padding-top:64px;padding-bottom:96px;max-width:900px">
	<h1 class="h1" style="margin-bottom:24px"><?= e($CurrentNode->name) ?></h1>
	<p style="margin:0 0 28px;font-family:Unbounded,sans-serif;font-size:22px;line-height:1.4">Дякую, люб’язний друже. Я постараюся відповісти на твоє повідомлення найближчим часом.</p>
	<a class="btn" href="<?= e($CurrentNode->getHref()) ?>">Написати ще</a>
</section>
<?php tinclude("_foot") ?>
