<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/WEB-INF/prepend.php");
$photoLightboxBlock = QNode::getInstance($_GET['id']);
?><figure class="figure photo_lightbox_block custom_block">
    <a href="<?= $photoLightboxBlock->tfields['photo']['url'] ?>" class="photo_lightbox" title="<?= htmlspecialchars($photoLightboxBlock->name) ?>" data-head="" data-link="#" source="<?= str_replace('/ithumb/','/o/',$photoLightboxBlock->tfields['photo']['thumburl']) ?>"><img src="<?= $photoLightboxBlock->tfields['photo']['thumburl'] ?>" width="580"  alt="<?= htmlspecialchars($photoLightboxBlock->name) ?>"></a>
    <figcaption><?= $photoLightboxBlock->name ?></figcaption>
</figure>
