<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/WEB-INF/prepend.php");
$photoLightboxBlock = QNode::getInstance($_GET['id']);
?><figure class="figure_big custom_block">
    <img src="<?=$photoLightboxBlock->tfields['photo']['url']?>" width="980" alt="<?=  htmlspecialchars($photoLightboxBlock->name)?>">
    <figcaption><?=$photoLightboxBlock->name?></figcaption>
</figure>
