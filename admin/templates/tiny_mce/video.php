<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/WEB-INF/prepend.php");
$video = QNode::getInstance($_GET['id']);
if ($video->nodeClassId != 280)
    die('wrong id');
?>
<div class="videoBlock">
    <span style="display:none;" id="VIDEO_START_<?= $video->id ?>_ID">&nbsp;</span>
    <div class="videoBlockTitle">
        <span class="videoLink">&nbsp;</span>
        <h3><?= $video->name ?></h3>
        <div class="clear">&nbsp;</div>
    </div>
    <a href="#"><img <?= $video->tfields['screen']['size_tag'] ?> alt="<?= $video->name ?>" src="<?= $video->tfields['screen']['url'] ?>"></a>
    <span style="display:none;" id="VIDEO_END">&nbsp;</span>
</div>
<p>&nbsp;</p>