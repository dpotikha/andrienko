<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/WEB-INF/prepend.php");
$photoLightboxBlock = QNode::getInstance($_GET['id']);
?><figure class="figure_left photo_lightbox_block">
    <a href="<?= $photoLightboxBlock->tfields['photo']['url'] ?>" class="photo_lightbox" title="<?= htmlspecialchars($photoLightboxBlock->name) ?>"  rel="gal"><img src="<?= $photoLightboxBlock->tfields['photo']['thumburl'] ?>" alt="<?= htmlspecialchars($photoLightboxBlock->name) ?>"></a>
    <figcaption><?= $photoLightboxBlock->name ?></figcaption>
</figure>

<p>&nbsp;</p>