<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/WEB-INF/prepend.php");
$photoGallery=QNode::getInstance($_GET['id']);
if ($photoGallery->nodeClassId != 429)
    die('wrong id');
?><div class="content__photo custom_block">
    <h3 class="h3"><?=$photoGallery->name?></h3>
    <div class="photoGalleryBlock" id="<?=$photoGallery->id?>"><img src="/_img/link_photogallery.jpg"></div>
</div>