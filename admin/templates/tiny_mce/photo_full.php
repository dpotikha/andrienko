<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/WEB-INF/prepend.php");
$photoFullBlock = QNode::getInstance($_GET['id']);
?>
</div>
</div>
<div class="full_width with_bg quote_container" title="<?=  htmlspecialchars($photoFullBlock->name)?>" style="background-image: url('<?=$photoFullBlock->tfields['photo']['url']?>');">
<div class="fixed_width">&nbsp;</div>
</div>
<div class="fixed_width">
<div class="content">
<figcaption><?=$photoFullBlock->name?></figcaption>
<p>&nbsp;</p>