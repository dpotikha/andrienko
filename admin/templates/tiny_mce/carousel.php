<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/WEB-INF/prepend.php");
$photoGalleryBlock = QNode::getInstance($_GET['id']);
$photoGallery=$photoGalleryBlock->fields['node_link']->getLinkedNode();
if ($photoGallery->nodeClassId != 429)
    die('wrong id');
//$photos = $photoGallery->getChildren('n.subtree_order',0,6);
//print_r($photos);
?><div class="content__photo">
    <h3 class="h3"><?=$photoGalleryBlock->name?></h3>
    <div class="photoGalleryBlock" id="<?=$photoGallery->id?>"><img src="/_img/link_photogallery.jpg"></div>
    <?php /*<button class="content__prev" id="cnt-photo-prev" sourceindex="5"></button>
    <button class="content__next" id="cnt-photo-next" sourceindex="6"></button>
    <ul data-allow-wrap="false" data-cycle-carousel-visible="6" data-cycle-next="#cnt-photo-next" data-cycle-prev="#cnt-photo-prev" data-cycle-slides="&gt; li" data-cycle-timeout="0" data-cycle-fx="carousel" class="cycle-slideshow" style="position: relative; overflow: hidden; width: 960px;">
        <div class="cycle-carousel-wrap" style="margin: 0px; padding: 0px; top: 0px; left: -480px; position: absolute; white-space: nowrap;">
            <? foreach($photos as $k=>$v){?>
            <li style="position: static; top: 0px; left: 0px; z-index: 100; opacity: 1; display: inline-block;" class="cycle-slide"><a data-link="#" data-head="" title="" href="<?=$v->tfields['photo']['url']?>" class="js-cb cboxElement" rel="fancygallery<?=$photoGallery->id?>"><img width="140" height="130" alt="" src="<?=$v->tfields['photo']['thumburl']?>"></a></li>
            <? }?>
            </div>
    </ul>*/?>
</div>
<p>&nbsp;</p>

<?php /*

<div class="newsGalleryBlock">
    <span style="display:none;" id="CAROUSEL_START_<?= $photoGallery->id ?>_ID">&nbsp;</span>
    <div class=" jcarousel-skin-tango">
        <div class="jcarousel-container jcarousel-container-horizontal" style="position: relative; display: block;">
            <div class="jcarousel-clip jcarousel-clip-horizontal" style="position: relative;">
                <ul class="jcarousel-list jcarousel-list-horizontal" id="mycarousel" style="overflow: hidden; position: relative; top: 0px; margin: 0px; padding: 0px; left: 0px; width: 1702px;">
                    <? if ($photos)
                        foreach ($photos as $k => $v) {
                            $k++;
                            ?>
                            <li class="jcarousel-item jcarousel-item-horizontal jcarousel-item-<?= $k ?> jcarousel-item-<?= $k ?>-horizontal" style="float: left; list-style: none outside none;" jcarouselindex="<?= $k ?>"><a href="#"><img width="168" height="119" alt="<?= $v->name ?>" src="<?= $v->tfields['photo']['thumburl'] ?>"></a></li>
    <? } ?>
                </ul>
            </div>
            <div class="jcarousel-prev jcarousel-prev-horizontal" style="display: block;" disabled="false">&nbsp;
            </div>
            <div class="jcarousel-next jcarousel-next-horizontal" style="display: block;" disabled="false">&nbsp;
            </div>
        </div>
    </div>
    <span style="display:none;" id="CAROUSEL_END">&nbsp;</span>
</div>*/?>