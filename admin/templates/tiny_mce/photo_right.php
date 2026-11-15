<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/WEB-INF/prepend.php");
$photoRightBlock = QNode::getInstance($_GET['id']);
?><figure class="figure_left">
    <a href="<?= $photoRightBlock->tfields['photo']['url'] ?>" class="photo_lightbox" title="<?= htmlspecialchars($photoRightBlock->name) ?>" ><img src="<?= $photoRightBlock->tfields['photo']['thumburl'] ?>" alt="<?= htmlspecialchars($photoRightBlock->name) ?>"></a>
    <figcaption><?= $photoRightBlock->name ?></figcaption>
</figure>

<p>&nbsp;</p>