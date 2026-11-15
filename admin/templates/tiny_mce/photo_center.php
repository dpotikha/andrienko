<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/WEB-INF/prepend.php");
$photoFullBlock = QNode::getInstance($_GET['id']);
?><figure class="figure">
    <img src="<?=str_replace('/ithumb/','/ithumb2/',$photoFullBlock->tfields['photo']['thumburl'])?>" width="580" alt="<?=  htmlspecialchars($photoFullBlock->name)?>">
    <figcaption><?=$photoFullBlock->name?></figcaption>
</figure>
